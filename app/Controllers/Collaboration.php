<?php

namespace App\Controllers;

use App\Models\ListModel;
use App\Models\ListCollaboratorModel;
use App\Models\ListInvitationModel;
use App\Models\UserModel;

class Collaboration extends BaseController
{
    /**
     * Send invitation to co-own a list
     */
    public function invite()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $listId = $this->request->getPost('list_id');
        $email = $this->request->getPost('email');
        $message = $this->request->getPost('message');

        if (!$listId || !$email) {
            return $this->response->setJSON(['success' => false, 'message' => 'List ID and email are required']);
        }

        $userId = $this->session->get('user_id');
        $listModel = new ListModel();
        $collaboratorModel = new ListCollaboratorModel();
        $invitationModel = new ListInvitationModel();

        // Check if user has permission to invite
        $list = $listModel->find($listId);
        if (!$list) {
            return $this->response->setJSON(['success' => false, 'message' => 'List not found']);
        }

        $canInvite = $listModel->isUserOwner($listId, $userId) || $collaboratorModel->canInvite($listId, $userId);
        if (!$canInvite) {
            return $this->response->setJSON(['success' => false, 'message' => 'You do not have permission to invite collaborators']);
        }

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid email address']);
        }

        // Check if trying to invite self
        $userModel = new UserModel();
        $currentUser = $userModel->find($userId);
        if ($currentUser && $currentUser['email'] === $email) {
            return $this->response->setJSON(['success' => false, 'message' => 'You cannot invite yourself']);
        }

        // Create invitation
        $invitation = $invitationModel->createInvitation($listId, $userId, $email, $message);

        if ($invitation) {
            log_message('info', "Invitation sent for list {$listId} to {$email} by user {$userId}");
            
            // TODO: Send email notification
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Invitation sent successfully',
                'invitation' => $invitation
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User is already invited or is a collaborator'
            ]);
        }
    }

    /**
     * Accept an invitation
     */
    public function accept($token)
    {
        if (!$this->isLoggedIn()) {
            $this->session->set('redirect_after_login', current_url());
            return redirect()->to('/login')->with('info', 'Please login or register to accept the invitation');
        }

        $userId = $this->session->get('user_id');
        $invitationModel = new ListInvitationModel();

        $invitation = $invitationModel->getInvitationByToken($token);

        if (!$invitation) {
            return redirect()->to('/dashboard')->with('error', 'Invitation not found or expired');
        }

        if ($invitation['status'] !== 'pending') {
            return redirect()->to('/dashboard')->with('error', 'This invitation has already been ' . $invitation['status']);
        }

        // Accept the invitation
        if ($invitationModel->acceptInvitation($token, $userId)) {
            log_message('info', "User {$userId} accepted invitation for list {$invitation['list_id']}");
            
            return redirect()->to('/dashboard/list/edit/' . $invitation['list_id'])
                ->with('success', 'You are now a co-owner of "' . $invitation['list_title'] . '"! You can now edit this list.');
        } else {
            return redirect()->to('/dashboard')->with('error', 'Failed to accept invitation. It may have expired.');
        }
    }

    /**
     * Reject an invitation
     */
    public function reject($token)
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login');
        }

        $invitationModel = new ListInvitationModel();
        $invitation = $invitationModel->getInvitationByToken($token);

        if (!$invitation) {
            return redirect()->to('/dashboard')->with('error', 'Invitation not found');
        }

        if ($invitationModel->rejectInvitation($token)) {
            log_message('info', "User {$this->session->get('user_id')} rejected invitation {$token}");
            
            return redirect()->to('/dashboard/invitations')
                ->with('success', 'Invitation declined');
        } else {
            return redirect()->to('/dashboard')->with('error', 'Failed to reject invitation');
        }
    }

    /**
     * Cancel an invitation (by inviter)
     */
    public function cancel($invitationId)
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $userId = $this->session->get('user_id');
        $invitationModel = new ListInvitationModel();

        if ($invitationModel->cancelInvitation($invitationId, $userId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Invitation cancelled']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to cancel invitation']);
        }
    }

    /**
     * Remove a collaborator from a list
     */
    public function remove()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $listId = $this->request->getPost('list_id');
        $collaboratorUserId = $this->request->getPost('user_id');

        if (!$listId || !$collaboratorUserId) {
            return $this->response->setJSON(['success' => false, 'message' => 'List ID and user ID are required']);
        }

        $userId = $this->session->get('user_id');
        $listModel = new ListModel();
        $collaboratorModel = new ListCollaboratorModel();

        // Only the owner can remove collaborators
        if (!$listModel->isUserOwner($listId, $userId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Only the list owner can remove collaborators']);
        }

        if ($collaboratorModel->removeCollaborator($listId, $collaboratorUserId)) {
            log_message('info', "User {$userId} removed collaborator {$collaboratorUserId} from list {$listId}");
            
            return $this->response->setJSON(['success' => true, 'message' => 'Collaborator removed']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to remove collaborator']);
        }
    }

    /**
     * Leave a list (remove self as collaborator)
     */
    public function leave()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $json = $this->request->getJSON();
        $listId = $json->list_id ?? $this->request->getPost('list_id');
        
        if (!$listId) {
            return $this->response->setJSON(['success' => false, 'message' => 'List ID is required']);
        }

        $userId = $this->session->get('user_id');
        $listModel = new ListModel();
        $collaboratorModel = new ListCollaboratorModel();

        // Cannot leave if you're the owner
        if ($listModel->isUserOwner($listId, $userId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'You cannot leave your own list. Transfer ownership first.']);
        }

        if ($collaboratorModel->removeCollaborator($listId, $userId)) {
            log_message('info', "User {$userId} left collaboration on list {$listId}");
            
            return $this->response->setJSON(['success' => true, 'message' => 'You have left the collaboration']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to leave collaboration']);
        }
    }

    /**
     * View pending invitations for current user
     */
    public function invitations()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $userId = $this->session->get('user_id');
        $invitationModel = new ListInvitationModel();

        $this->data['invitations'] = $invitationModel->getPendingInvitationsForUser($userId);
        $this->data['title'] = 'Pending Invitations';

        return view('dashboard/invitations', $this->data);
    }

    /**
     * Get collaborators for a list (AJAX)
     */
    public function getCollaborators($listId)
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $userId = $this->session->get('user_id');
        $listModel = new ListModel();

        // Check if user has access to this list
        if (!$listModel->canUserEdit($listId, $userId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $collaboratorModel = new ListCollaboratorModel();
        $invitationModel = new ListInvitationModel();

        $collaborators = $collaboratorModel->getListCollaborators($listId);
        $pendingInvitations = $invitationModel->getListInvitations($listId);

        // Filter only pending invitations
        $pendingInvitations = array_filter($pendingInvitations, function($inv) {
            return $inv['status'] === 'pending';
        });

        return $this->response->setJSON([
            'success' => true,
            'collaborators' => $collaborators,
            'invitations' => array_values($pendingInvitations),
            'is_owner' => $listModel->isUserOwner($listId, $userId)
        ]);
    }

    /**
     * Update collaborator permissions
     */
    public function updatePermissions()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $listId = $this->request->getPost('list_id');
        $collaboratorUserId = $this->request->getPost('user_id');
        $canInvite = $this->request->getPost('can_invite') === 'true';

        $userId = $this->session->get('user_id');
        $listModel = new ListModel();
        $collaboratorModel = new ListCollaboratorModel();

        // Only the owner can update permissions
        if (!$listModel->isUserOwner($listId, $userId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Only the list owner can update permissions']);
        }

        $updated = $collaboratorModel->where('list_id', $listId)
            ->where('user_id', $collaboratorUserId)
            ->set(['can_invite' => $canInvite ? 1 : 0])
            ->update();

        if ($updated) {
            return $this->response->setJSON(['success' => true, 'message' => 'Permissions updated']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update permissions']);
        }
    }
}

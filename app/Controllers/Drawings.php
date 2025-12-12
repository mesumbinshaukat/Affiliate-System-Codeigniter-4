<?php

namespace App\Controllers;

use App\Models\DrawingModel;
use App\Models\DrawingParticipantModel;
use App\Models\UserModel;
use App\Models\ListModel;

class Drawings extends BaseController
{
    public function index()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $drawingModel = new DrawingModel();
        $userId = $this->session->get('user_id');

        // Get drawings created by user
        $this->data['myDrawings'] = $drawingModel->getUserDrawings($userId);

        // Get drawings user is participating in
        $participantModel = new DrawingParticipantModel();
        $this->data['participatingDrawings'] = $participantModel->getUserParticipations($userId);

        return view('drawings/index', $this->data);
    }

    public function create()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        if (strtolower($this->request->getMethod()) === 'post') {
            $drawingModel = new DrawingModel();
            $userId = $this->session->get('user_id');

            $data = [
                'creator_id' => $userId,
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'event_date' => $this->request->getPost('event_date'),
                'status' => 'pending',
            ];

            if ($drawingModel->insert($data)) {
                $drawingId = $drawingModel->getInsertID();
                return redirect()->to('/drawings/edit/' . $drawingId)->with('success', 'Drawing created successfully');
            }

            $errors = $drawingModel->errors();
            return redirect()->back()->with('error', implode(', ', $errors))->withInput();
        }

        return view('drawings/create', $this->data);
    }

    public function edit($drawingId)
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $drawingModel = new DrawingModel();
        $drawing = $drawingModel->find($drawingId);

        if (!$drawing || $drawing['creator_id'] != $this->session->get('user_id')) {
            return redirect()->to('/drawings')->with('error', 'Drawing not found or access denied');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $data = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'event_date' => $this->request->getPost('event_date'),
            ];

            if ($drawingModel->update($drawingId, $data)) {
                return redirect()->back()->with('success', 'Drawing updated successfully');
            }

            $errors = $drawingModel->errors();
            return redirect()->back()->with('error', implode(', ', $errors))->withInput();
        }

        $this->data['drawing'] = $drawing;
        $this->data['participants'] = $drawingModel->getDrawingParticipants($drawingId);

        return view('drawings/edit', $this->data);
    }

    public function addParticipant($drawingId)
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $drawingModel = new DrawingModel();
        $drawing = $drawingModel->find($drawingId);

        if (!$drawing || $drawing['creator_id'] != $this->session->get('user_id')) {
            return redirect()->to('/drawings')->with('error', 'Drawing not found or access denied');
        }

        if ($drawing['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Cannot add participants to a drawn or completed drawing');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $userModel = new UserModel();
            $username = $this->request->getPost('username');
            $user = $userModel->where('username', $username)->first();

            if (!$user) {
                return redirect()->back()->with('error', 'User not found')->withInput();
            }

            $participantModel = new DrawingParticipantModel();
            $existingParticipant = $participantModel->where('drawing_id', $drawingId)
                ->where('user_id', $user['id'])
                ->first();

            if ($existingParticipant) {
                return redirect()->back()->with('error', 'User is already a participant')->withInput();
            }

            $participantData = [
                'drawing_id' => $drawingId,
                'user_id' => $user['id'],
            ];
            
            // Add status field if the column exists in the database
            $db = \Config\Database::connect();
            $fields = $db->getFieldData('drawing_participants');
            $statusFieldExists = false;
            foreach ($fields as $field) {
                if ($field->name === 'status') {
                    $statusFieldExists = true;
                    break;
                }
            }
            
            if ($statusFieldExists) {
                $participantData['status'] = 'pending';
            }

            if ($participantModel->insert($participantData)) {
                return redirect()->back()->with('success', 'Participant added successfully');
            }

            $errors = $participantModel->errors();
            return redirect()->back()->with('error', implode(', ', $errors))->withInput();
        }

        $this->data['drawing'] = $drawing;
        return view('drawings/add_participant', $this->data);
    }

    public function removeParticipant($drawingId, $participantId)
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $drawingModel = new DrawingModel();
        $drawing = $drawingModel->find($drawingId);

        if (!$drawing || $drawing['creator_id'] != $this->session->get('user_id')) {
            return redirect()->to('/drawings')->with('error', 'Drawing not found or access denied');
        }

        $participantModel = new DrawingParticipantModel();
        $participant = $participantModel->find($participantId);

        if (!$participant || $participant['drawing_id'] != $drawingId) {
            return redirect()->back()->with('error', 'Participant not found');
        }

        if ($participantModel->delete($participantId)) {
            return redirect()->back()->with('success', 'Participant removed successfully');
        }

        return redirect()->back()->with('error', 'Failed to remove participant');
    }

    public function draw($drawingId)
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $drawingModel = new DrawingModel();
        $drawing = $drawingModel->find($drawingId);

        if (!$drawing || $drawing['creator_id'] != $this->session->get('user_id')) {
            return redirect()->to('/drawings')->with('error', 'Drawing not found or access denied');
        }

        if ($drawing['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Drawing has already been drawn');
        }

        $participantModel = new DrawingParticipantModel();
        $participants = $participantModel->getParticipantsByDrawing($drawingId);

        if (count($participants) < 2) {
            return redirect()->back()->with('error', 'At least 2 participants are required to draw lots');
        }

        // Check if all participants have accepted
        $acceptedParticipants = $participantModel->getAcceptedParticipants($drawingId);
        if (count($acceptedParticipants) < count($participants)) {
            return redirect()->back()->with('error', 'All participants must accept the invitation before drawing lots');
        }

        // Shuffle participants and assign each person a random other person
        $userIds = array_column($participants, 'user_id');
        $shuffledIds = $userIds;
        shuffle($shuffledIds);

        // Ensure no one draws themselves
        $maxAttempts = 100;
        $attempt = 0;
        while ($this->hasConflict($userIds, $shuffledIds) && $attempt < $maxAttempts) {
            shuffle($shuffledIds);
            $attempt++;
        }

        if ($this->hasConflict($userIds, $shuffledIds)) {
            return redirect()->back()->with('error', 'Failed to create valid drawing. Please try again.');
        }

        // Update assignments
        for ($i = 0; $i < count($userIds); $i++) {
            $assignedUserId = $shuffledIds[$i];
            
            // Get the list of the assigned user
            $listModel = new ListModel();
            $userList = $listModel->where('user_id', $assignedUserId)
                ->where('status', 'published')
                ->orderBy('created_at', 'DESC')
                ->first();

            $participantModel->update(
                $participants[$i]['id'],
                [
                    'assigned_to_user_id' => $assignedUserId,
                    'list_id' => $userList ? $userList['id'] : null,
                ]
            );
        }

        // Update drawing status
        $drawingModel->update($drawingId, ['status' => 'drawn']);

        return redirect()->to('/drawings/view/' . $drawingId)->with('success', 'Drawing completed successfully!');
    }

    public function view($drawingId)
    {
        $drawingModel = new DrawingModel();
        $drawing = $drawingModel->getDrawingWithParticipants($drawingId);

        if (!$drawing) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Check if user is creator or participant
        $userId = $this->isLoggedIn() ? $this->session->get('user_id') : null;
        $isCreator = $userId && $drawing['creator_id'] == $userId;

        $participantModel = new DrawingParticipantModel();
        $participants = $participantModel->select('drawing_participants.*, 
                                                   users.username, users.first_name, users.last_name,
                                                   assigned_users.username as assigned_username, assigned_users.first_name as assigned_first_name, assigned_users.last_name as assigned_last_name,
                                                   lists.title as list_title, lists.slug as list_slug')
            ->join('users', 'users.id = drawing_participants.user_id')
            ->join('users as assigned_users', 'assigned_users.id = drawing_participants.assigned_to_user_id', 'left')
            ->join('lists', 'lists.id = drawing_participants.list_id', 'left')
            ->where('drawing_participants.drawing_id', $drawingId)
            ->findAll();

        // If drawing is drawn, only show assignments to creator and assigned person
        if ($drawing['status'] === 'drawn') {
            $participants = array_filter($participants, function($p) use ($userId, $isCreator) {
                return $isCreator || $p['user_id'] == $userId;
            });
        }

        $this->data['drawing'] = $drawing;
        $this->data['participants'] = $participants;
        $this->data['isCreator'] = $isCreator;
        $this->data['currentUserId'] = $userId;

        return view('drawings/view', $this->data);
    }

    public function acceptInvitation($participantId)
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $userId = $this->session->get('user_id');
        $participantModel = new DrawingParticipantModel();

        // Fetch participant with drawing info for authorization check
        $participant = $participantModel->select('drawing_participants.*, drawings.creator_id, drawings.id as drawing_id')
            ->join('drawings', 'drawings.id = drawing_participants.drawing_id')
            ->where('drawing_participants.id', $participantId)
            ->first();

        // Comprehensive authorization checks
        if (!$participant) {
            return redirect()->to('/drawings')->with('error', 'Invitation not found');
        }

        // Check if current user is the invited participant
        if ($participant['user_id'] != $userId) {
            return redirect()->to('/drawings')->with('error', 'You are not authorized to accept this invitation');
        }

        // Check if invitation is still pending
        if (($participant['status'] ?? 'pending') !== 'pending') {
            return redirect()->back()->with('error', 'This invitation has already been processed');
        }

        // Update the status to accepted
        if ($participantModel->acceptParticipation($participantId)) {
            return redirect()->to('/drawings')->with('success', 'U hebt de uitnodiging geaccepteerd!');
        }

        return redirect()->back()->with('error', 'Uitnodiging accepteren mislukt');
    }

    public function declineInvitation($participantId)
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $userId = $this->session->get('user_id');
        $participantModel = new DrawingParticipantModel();

        // Fetch participant with drawing info for authorization check
        $participant = $participantModel->select('drawing_participants.*, drawings.creator_id, drawings.id as drawing_id')
            ->join('drawings', 'drawings.id = drawing_participants.drawing_id')
            ->where('drawing_participants.id', $participantId)
            ->first();

        // Comprehensive authorization checks
        if (!$participant) {
            return redirect()->to('/drawings')->with('error', 'Invitation not found');
        }

        // Check if current user is the invited participant
        if ($participant['user_id'] != $userId) {
            return redirect()->to('/drawings')->with('error', 'You are not authorized to decline this invitation');
        }

        // Check if invitation is still pending
        if (($participant['status'] ?? 'pending') !== 'pending') {
            return redirect()->back()->with('error', 'This invitation has already been processed');
        }

        // Update the status to declined
        if ($participantModel->declineParticipation($participantId)) {
            return redirect()->to('/drawings')->with('success', 'U hebt de uitnodiging geweigerd');
        }

        return redirect()->back()->with('error', 'Uitnodiging weigeren mislukt');
    }

    private function hasConflict($original, $shuffled)
    {
        for ($i = 0; $i < count($original); $i++) {
            if ($original[$i] === $shuffled[$i]) {
                return true;
            }
        }
        return false;
    }
}

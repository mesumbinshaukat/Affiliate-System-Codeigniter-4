# List Co-Owner & Collaboration Feature - Complete Documentation

## 📋 Overview

The List Co-Owner feature allows users to invite others to collaborate on their lists. Co-owners can fully edit lists, add/remove products, and manage sections just like the original owner.

## ✨ Key Features

### For List Owners
- **Invite by Email** - Send invitations to any email address
- **Personal Messages** - Include custom messages with invitations
- **Manage Co-Owners** - View, remove, and manage collaborator permissions
- **Track Invitations** - See pending, accepted, and rejected invitations
- **Owner Badge** - Clear visual distinction as list owner

### For Invited Users
- **Email Notifications** - Receive invitation with personal message
- **Accept/Reject** - Simple one-click acceptance or rejection
- **Full Edit Access** - Edit list details, add/remove products, manage sections
- **Dashboard Integration** - See collaborated lists alongside owned lists
- **Leave Anytime** - Option to leave collaboration if needed

### Smart Features
- **Auto Account Linking** - Invitations automatically link when user registers
- **7-Day Expiry** - Invitations expire after 7 days for security
- **Duplicate Prevention** - Can't invite same person twice
- **Self-Invite Protection** - Can't invite yourself
- **Permission Checks** - Robust permission system prevents unauthorized access

## 🗄️ Database Schema

### `list_collaborators` Table
```sql
id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
list_id             INT UNSIGNED (FK to lists)
user_id             INT UNSIGNED (FK to users)
role                ENUM('owner', 'editor') DEFAULT 'editor'
can_invite          TINYINT(1) DEFAULT 0
created_at          DATETIME
updated_at          DATETIME

UNIQUE KEY (list_id, user_id)
INDEX (list_id, user_id)
```

### `list_invitations` Table
```sql
id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
list_id             INT UNSIGNED (FK to lists)
inviter_id          INT UNSIGNED (FK to users)
invitee_email       VARCHAR(255)
invitee_id          INT UNSIGNED NULL (FK to users)
token               VARCHAR(64) UNIQUE
status              ENUM('pending', 'accepted', 'rejected', 'expired')
message             TEXT NULL
expires_at          DATETIME
responded_at        DATETIME NULL
created_at          DATETIME
updated_at          DATETIME

INDEX (token, invitee_email, invitee_id, status)
```

## 🚀 Installation

### Option 1: Command Line
```bash
cd /path/to/your/project
php spark migrate
```

### Option 2: Web-Based Migration
1. Visit: `http://localhost:8080/run_migration_collaborators.php`
2. Follow the on-screen instructions
3. **IMPORTANT:** Delete the migration file after running

## 📖 User Guide

### How to Invite a Co-Owner

1. **Go to Your List**
   - Navigate to Dashboard → Lists
   - Click on the list you want to share

2. **Open Collaboration Tab**
   - Click on the "Samenwerken" (Collaborate) tab
   - You'll see the invitation form

3. **Send Invitation**
   - Enter the person's email address
   - Optionally add a personal message
   - Click "Uitnodigen" (Invite)

4. **Track Status**
   - Pending invitations appear below the form
   - You can cancel invitations anytime

### How to Accept an Invitation

1. **Check Invitations**
   - Go to Dashboard → Invitations
   - Or click the link in the invitation email

2. **Review Details**
   - See who invited you
   - Read the personal message
   - View list details

3. **Accept or Reject**
   - Click "Accepteren" to become a co-owner
   - Click "Afwijzen" to decline
   - Once accepted, you can edit the list immediately

### How to Manage Co-Owners (Owner Only)

1. **View Collaborators**
   - Go to list edit page
   - Click "Samenwerken" tab
   - See all current co-owners

2. **Remove Co-Owner**
   - Click the X button next to their name
   - Confirm removal
   - They lose access immediately

3. **Update Permissions**
   - Grant "can invite" permission to trusted co-owners
   - They can then invite others on your behalf

### How to Leave a Collaboration

1. **Access the List**
   - Go to the collaborated list edit page
   - Open "Samenwerken" tab

2. **Leave**
   - Click "Samenwerking verlaten"
   - Confirm your choice
   - You'll lose access to the list

## 🔧 Technical Implementation

### Models

#### `ListCollaboratorModel`
```php
// Key Methods
isCollaborator(int $listId, int $userId): bool
isOwner(int $listId, int $userId): bool
canEdit(int $listId, int $userId): bool
canInvite(int $listId, int $userId): bool
getListCollaborators(int $listId): array
getUserCollaborations(int $userId): array
addCollaborator(int $listId, int $userId, string $role, bool $canInvite): bool
removeCollaborator(int $listId, int $userId): bool
```

#### `ListInvitationModel`
```php
// Key Methods
createInvitation(int $listId, int $inviterId, string $email, ?string $message): array|false
getPendingInvitationsForUser(int $userId): array
getPendingInvitationsForEmail(string $email): array
acceptInvitation(string $token, int $userId): bool
rejectInvitation(string $token): bool
cancelInvitation(int $invitationId, int $inviterId): bool
linkInvitationsToUser(string $email, int $userId): int
```

#### `ListModel` (Extended)
```php
// New Methods
canUserEdit(int $listId, int $userId): bool
isUserOwner(int $listId, int $userId): bool
getUserAccessibleLists(int $userId, bool $includePrivate): array
```

### Controllers

#### `CollaborationController`
```php
Routes:
POST   /collaboration/invite               - Send invitation
GET    /collaboration/accept/{token}       - Accept invitation
GET    /collaboration/reject/{token}       - Reject invitation
POST   /collaboration/cancel/{id}          - Cancel invitation (inviter only)
POST   /collaboration/remove               - Remove collaborator (owner only)
POST   /collaboration/leave                - Leave collaboration
GET    /collaboration/list/{id}/collaborators - Get collaborators (AJAX)
POST   /collaboration/permissions/update   - Update permissions (owner only)
GET    /dashboard/invitations              - View pending invitations
```

### Permission System

The permission system works as follows:

1. **List Access Check** (in Dashboard::editList)
```php
if (!$list || !$listModel->canUserEdit($listId, $userId)) {
    return redirect()->to('/dashboard')->with('error', 'Access denied');
}
```

2. **Owner Verification** (for privileged actions)
```php
$isOwner = $listModel->isUserOwner($listId, $userId);
```

3. **Collaborator Check**
```php
$canEdit = $collaboratorModel->canEdit($listId, $userId);
$canInvite = $collaboratorModel->canInvite($listId, $userId);
```

### Frontend Components

#### Collaborator Management Component
Located: `app/Views/partials/collaborator_management.php`

Features:
- Invitation form (owner only)
- Live collaborator list
- Pending invitations display
- Remove/cancel buttons
- Leave collaboration option
- AJAX-based real-time updates

#### Invitations Page
Located: `app/Views/dashboard/invitations.php`

Features:
- List of pending invitations
- Invitation details (who invited, when, message)
- Accept/reject buttons
- Expiry date display

## 🔒 Security Considerations

### Permission Checks
✅ All edit actions verify user has edit rights
✅ Only owners can remove collaborators
✅ Only owners can modify permissions
✅ Users can't invite themselves
✅ Duplicate invitations prevented

### Token Security
✅ 64-character random tokens for invitations
✅ Tokens are unique and unpredictable
✅ Single-use tokens (accepted/rejected)
✅ 7-day automatic expiry

### Data Integrity
✅ Foreign key constraints prevent orphaned records
✅ Cascade delete removes collaborators when list deleted
✅ Unique constraints prevent duplicate collaborations
✅ Transaction-based operations for consistency

### Input Validation
✅ Email validation on invitations
✅ User existence checks
✅ Collaborator existence verification
✅ Owner permission validation

## 📊 Use Cases

### 1. Couple's Wedding List
```
Owner: Sarah
Co-Owner: John (her fiancé)
Use: Manage wedding wishlist together
```

### 2. Family Birthday List
```
Owner: Mom
Co-Owners: Dad, Sister, Brother
Use: Coordinate birthday gift ideas
```

### 3. Roommate Apartment List
```
Owner: Alice
Co-Owners: Bob, Charlie (roommates)
Use: Shared apartment wishlist
```

### 4. Event Planning List
```
Owner: Event Organizer
Co-Owners: Co-organizers (2-3 people)
Use: Manage event supplies and gifts
```

### 5. Office Team List
```
Owner: Team Lead
Co-Owners: Team Members
Use: Office supplies or team wishlist
```

## 🐛 Troubleshooting

### Invitation Not Received
**Problem:** Invitee didn't receive invitation
**Solution:** 
- Check spam folder
- Verify email address is correct
- Check invitations page manually: `/dashboard/invitations`

### Can't Remove Co-Owner
**Problem:** Remove button doesn't work
**Solution:**
- Verify you're the owner (not a co-owner)
- Can't remove yourself if you're owner
- Check browser console for errors

### Invitation Expired
**Problem:** Invitation shows as expired
**Solution:**
- Invitations expire after 7 days
- Owner can send a new invitation
- Old invitation tokens can't be reused

### Permission Denied
**Problem:** Co-owner can't edit list
**Solution:**
- Verify user accepted the invitation
- Check collaborator appears in "Samenwerken" tab
- Owner may have removed access

### Can't Leave Collaboration
**Problem:** "Leave collaboration" button doesn't work
**Solution:**
- Owners can't leave their own lists
- Must transfer ownership first (future feature)
- Contact original owner to remove you

## 🔄 Workflow Diagrams

### Invitation Flow
```
Owner → Send Invitation (email)
    ↓
Email sent to invitee
    ↓
Invitee clicks link
    ↓
[Has Account?]
    YES → Accept → Become Co-Owner
    NO  → Register → Accept → Become Co-Owner
```

### Collaboration Lifecycle
```
1. Owner creates list
2. Owner invites co-owner
3. Invitation sent (status: pending)
4. Invitee accepts (status: accepted)
5. Collaborator record created
6. Co-owner can now edit list
7. Owner can remove co-owner anytime
8. Or co-owner can leave anytime
```

## 📝 API Endpoints

### Send Invitation
```
POST /collaboration/invite
Body: {
    list_id: number,
    email: string,
    message: string (optional)
}
Response: {
    success: boolean,
    message: string,
    invitation: object
}
```

### Get Collaborators
```
GET /collaboration/list/{listId}/collaborators
Response: {
    success: boolean,
    collaborators: array,
    invitations: array,
    is_owner: boolean
}
```

### Remove Collaborator
```
POST /collaboration/remove
Body: {
    list_id: number,
    user_id: number
}
Response: {
    success: boolean,
    message: string
}
```

## 🎯 Future Enhancements

### Planned Features
- [ ] Email notifications for invitations
- [ ] Transfer ownership functionality
- [ ] Activity log for collaborations
- [ ] Permission levels (view-only, edit, admin)
- [ ] Bulk invite from contact list
- [ ] Collaboration analytics
- [ ] Invitation link expiry customization
- [ ] Resend invitation option
- [ ] In-app notification system
- [ ] Mobile app support

### Potential Improvements
- [ ] Role-based permissions (viewer, editor, admin)
- [ ] Comment system for collaborators
- [ ] Real-time updates with WebSockets
- [ ] Conflict resolution for simultaneous edits
- [ ] Version history and rollback
- [ ] Collaboration templates
- [ ] Integration with calendar apps
- [ ] Share via social media

## 📞 Support

### Common Questions

**Q: How many co-owners can I have?**
A: Unlimited! Invite as many people as you need.

**Q: Can co-owners invite others?**
A: Yes, if the owner grants them "can invite" permission.

**Q: What happens if I delete my account?**
A: Your owned lists transfer to first co-owner, or are deleted if none.

**Q: Can I see who made changes?**
A: Not currently, but activity logging is planned for future.

**Q: Do co-owners get notified of changes?**
A: Not currently, but notifications are planned for future.

### Contact

For bugs or feature requests:
- Check existing issues on GitHub
- Create new issue with detailed description
- Include screenshots if applicable

## 📄 License

This feature is part of Maakjelijstje.nl Affiliate System and follows the same MIT License.

---

**Version:** 1.0.0  
**Last Updated:** January 12, 2026  
**Author:** Development Team

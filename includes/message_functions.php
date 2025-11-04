<?php
/**
 * Message Functions for Zanvarsity
 */

/**
 * Send a message
 * 
 * @param int $sender_id The ID of the user sending the message
 * @param int|null $recipient_id The ID of the recipient (null for broadcast to all)
 * @param string $subject The message subject
 * @param string $message The message content
 * @param mysqli $conn Database connection
 * @return bool|string True on success, error message on failure
 */
function send_message($sender_id, $recipient_id, $subject, $message, $conn) {
    $is_broadcast = ($recipient_id === null) ? 1 : 0;
    
    $sql = "INSERT INTO messages (sender_id, recipient_id, subject, message, is_broadcast) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissi", $sender_id, $recipient_id, $subject, $message, $is_broadcast);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return $conn->error;
    }
}

/**
 * Get messages for a user
 * 
 * @param int $user_id The ID of the user
 * @param mysqli $conn Database connection
 * @param bool $unread_only Whether to get only unread messages
 * @return array Array of messages
 */
function get_messages($user_id, $conn, $unread_only = false) {
    $messages = [];
    
    $sql = "SELECT m.*, u1.first_name as sender_first_name, u1.last_name as sender_last_name,
                   u2.first_name as recipient_first_name, u2.last_name as recipient_last_name
            FROM messages m
            LEFT JOIN users u1 ON m.sender_id = u1.id
            LEFT JOIN users u2 ON m.recipient_id = u2.id
            WHERE (m.recipient_id = ? OR (m.is_broadcast = 1 AND m.sender_id != ?))";
    
    if ($unread_only) {
        $sql .= " AND m.is_read = 0";
    }
    
    $sql .= " ORDER BY m.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    
    return $messages;
}

/**
 * Get a single message by ID
 * 
 * @param int $message_id The ID of the message
 * @param int $user_id The ID of the user requesting the message
 * @param mysqli $conn Database connection
 * @return array|bool Message data or false if not found/unauthorized
 */
function get_message($message_id, $user_id, $conn) {
    $sql = "SELECT m.*, u1.first_name as sender_first_name, u1.last_name as sender_last_name,
                   u2.first_name as recipient_first_name, u2.last_name as recipient_last_name
            FROM messages m
            LEFT JOIN users u1 ON m.sender_id = u1.id
            LEFT JOIN users u2 ON m.recipient_id = u2.id
            WHERE m.id = ? AND (m.recipient_id = ? OR m.sender_id = ? OR (m.is_broadcast = 1 AND m.sender_id != ?))";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $message_id, $user_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Mark as read if the current user is the recipient
        $message = $result->fetch_assoc();
        if ($message['recipient_id'] == $user_id && !$message['is_read']) {
            mark_message_as_read($message_id, $conn);
        }
        return $message;
    }
    
    return false;
}

/**
 * Mark a message as read
 * 
 * @param int $message_id The ID of the message
 * @param mysqli $conn Database connection
 * @return bool True on success, false on failure
 */
function mark_message_as_read($message_id, $conn) {
    $sql = "UPDATE messages SET is_read = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $message_id);
    return $stmt->execute();
}

/**
 * Get list of users for the message recipient dropdown
 * 
 * @param int $current_user_id The ID of the current user (to exclude from the list)
 * @param mysqli $conn Database connection
 * @param string $user_role The role of the current user
 * @return array List of users
 */
function get_recipient_list($current_user_id, $conn, $user_role) {
    $users = [];
    
    $sql = "SELECT id, first_name, last_name, role FROM users WHERE id != ?";
    
    // If the user is a dean, they can only message admins
    if ($user_role === 'dean') {
        $sql .= " AND role IN ('admin', 'super_admin')";
    }
    // If the user is an admin, they can message everyone except other admins (unless super_admin)
    elseif (in_array($user_role, ['admin', 'super_admin'])) {
        $sql .= " AND (role != 'admin' AND role != 'super_admin')";
    }
    // Regular users can't send direct messages (they can only reply to received messages)
    else {
        return [];
    }
    
    $sql .= " ORDER BY first_name, last_name";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    return $users;
}
?>

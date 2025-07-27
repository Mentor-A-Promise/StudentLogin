
<?php

$headerType = 'logout';  
require_once '../includes/header.php';

include("../db/connect.php");

/*require_once './includes/header.php';

session_start();
$conn = new mysqli("localhost", "appsmat1_sandboxs", "Granite1@2", "appsmat1_sandboxs");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}*/

//$loggedInUserId = 1;
//$loggedInUserId = htmlspecialchars($_SESSION['loginid']);

$loggedInUserId = '';
$loggedInUserName = '';

if (isset($_SESSION['username']) && isset($_SESSION['loginid'])) {
    // Fetch session variables
    $loggedInUserName = htmlspecialchars($_SESSION['username']);
    $loggedInUserId = htmlspecialchars($_SESSION['loginid']);
    
    echo '<div class="d-flex justify-content-between align-items-center m-4">';
    // Display username
    echo '<h3>Logged in as student: ' . $loggedInUserName . '</h3>';
    // Add an icon for messages
    echo '<a href="../Messaging/messagetutors.php" class="message-icon">
            <i class="fas fa-comment fa-2x"></i><span class="badge badge-light"></span>
          </a>';
    echo '</div>';
} else {
    // User not logged in
    echo '<div class="alert alert-warning m-4">';
    echo '<h3>Please log in to access this page.</h3>';
    echo '<a href="index.php" class="btn btn-primary">Login</a>'; // Redirect to login page
    echo '</div>';
}

$receiverId = isset($_GET['receiver_id']) ? intval($_GET['receiver_id']) : 0;

$username = "SELECT username FROM login WHERE loginid = ?";
$usernamestmt = $con->prepare($username);
$usernamestmt->bind_param("i", $loggedInUserId);
$usernamestmt->execute();
$usernameresult = $usernamestmt->get_result();
if ($usernameresult->num_rows > 0) {
    $row = $usernameresult->fetch_assoc();
    $loggedInUserName = $row['username'];
}
$usernamestmt->close();

// Fetch messages
$sql = "SELECT m.id, m.sender_id, m.receiver_id, m.message_content, m.createdOn, m.parent_message_id, u1.username AS sender, u2.username AS receiver
        FROM messages m
        JOIN login u1 ON m.sender_id = u1.loginid
        JOIN login u2 ON m.receiver_id = u2.loginid
        WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.createdOn ASC";

$stmt = $con->prepare($sql);
$stmt->bind_param("iiii", $loggedInUserId, $receiverId, $receiverId, $loggedInUserId);
$stmt->execute();
$result = $stmt->get_result();

$allMessages = [];
while ($row = $result->fetch_assoc()) {
    $allMessages[$row['id']] = $row;
    $allMessages[$row['id']]['replies'] = [];
}

// Build the nested message structure
foreach ($allMessages as &$message) {
    if ($message['parent_message_id'] != 0) {
        $allMessages[$message['parent_message_id']]['replies'][] = &$message;
    }
}
unset($message);

// Filter only the top-level messages
$messages = array_filter($allMessages, function($message) {
    return $message['parent_message_id'] === 0;
}); 
$stmt->close();
$con->close();
?>

<?php
function displayMessages($messages, $loggedInUserId, $level = 0) {
    foreach ($messages as $message) {
        $alignmentClass = '';
        $marginLeft = $level * 20;  // Indent nested replies
        //echo $message['parent_message_id'];
        if ($message['parent_message_id'] === 0) {
            $alignmentClass = $message['sender_id'] == $loggedInUserId ? 'sent' : 'received';
            echo "<div class='message-container $alignmentClass' data-message-id='{$message['id']}'>";
        }else{
            echo "<div class='message-container' data-message-id='{$message['id']}' style='margin-left: {$marginLeft}px;'>";
        }
        
        $renderedContent = htmlspecialchars_decode($message['message_content'], ENT_QUOTES);
        $renderedContent = stripslashes($renderedContent);

        echo "<div class='message-content'>";
        echo "<strong>{$message['sender']}</strong>";
        //echo "<p>{$message['id']}</p>";
        echo "<p>{$renderedContent}</p>";
        echo "<div class='timestamp'>{$message['createdOn']}</div>";
        echo "</div>";
        //echo "<button onclick='openReplyForm({$message['id']})'>Reply</button>";
               
        
        // Display replies recursively
        if (!empty($message['replies'])) {
            echo "<div class='replies-container'>";
            displayMessages($message['replies'], $loggedInUserId, $level + 1);
            echo "</div>";
        }
        
        echo "</div>";
        echo "<div class='separator'></div>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Message Thread</title>
    <link rel="stylesheet" href="messageThreadStyles.css">
        <!-- Quill editor core -->
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>


    <!-- Module Faz quill emoji -->
    <link
    rel="stylesheet"
    href="https://unpkg.com/faz-quill-emoji@0.1.3/dist/faz.quill.emoji.css"
    type="text/css"
    />
    <script src="https://unpkg.com/faz-quill-emoji@0.1.3"></script>
</head>
<body>
<!-- <h4>Logged in as : <?php echo $loggedInUserName?> </h4> -->
<div class='all-messages-container'>
    <div id="messagesContainer">
        <?php displayMessages($messages, $loggedInUserId); ?>
    </div>

    <div id="contextMenu" class="context-menu">
        <button onclick="handleReply()">Reply</button>
    </div>

    <!-- Reply Form -->
    <form id="messageForm" onsubmit="sendReply(event)">
        <input type="hidden" id="parent_message_id" name="parent_message_id">
        <div id="replyMessageContainer"></div><br>
        <button class="green-button" type="submit">Send Message</button>
    </form>
</div>

<script>
    let selectedParentId = null;

    function openReplyForm(parentId) {
        document.getElementById('parent_message_id').value = parentId;
        //alert('Inside openReplyForm : Parent ID = '+document.getElementById('parent_message_id').value);
        document.getElementById('messageForm').scrollIntoView({ behavior: 'smooth' });
    }

    document.querySelectorAll('.message-container').forEach((messageDiv) => {
        messageDiv.addEventListener('contextmenu', (event) => {
            event.preventDefault();  // Prevent default right-click menu

            //alert('Inside querySelectorAll : Parent Id = '+selectedParentId);
            //alert('DIV tag is '+messageDiv.innerHTML);

            if (messageDiv.innerHTML.indexOf('message-container') >= 0) {
                return;
            }
            // Store the message ID
            selectedParentId = messageDiv.getAttribute('data-message-id');
            
            // Position the custom context menu at the cursor's position
            const contextMenu = document.getElementById('contextMenu');
            contextMenu.style.display = 'block';
            contextMenu.style.left = `${event.pageX}px`;
            contextMenu.style.top = `${event.pageY}px`;
            
        });
    });

    function handleReply() {
        alert('Inside handleReply() ParentId ='+selectedParentId);
        if (selectedParentId !== 0) {
            openReplyForm(selectedParentId);
        }
        hideContextMenu();
    }

    document.addEventListener('click', () => {
        hideContextMenu();
    });

    function hideContextMenu() {
        document.getElementById('contextMenu').style.display = 'none';
    }

    let quill;
    document.addEventListener('DOMContentLoaded', function() {
        quill = new Quill('#replyMessageContainer', {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: ['bold', 'italic', 'underline', 'strike', 'faz-emoji'],
                    handlers: {
                        'faz-emoji': true  
                    },
                },
                fazEmoji: {
                    collection: 'fluent-emoji'
                }
            }
        });
    });

    let editorInstance;
        // JavaScript to handle reply submission
        function sendReply(event) {
            event.preventDefault();
            //var parentId = null;

            const messageContent = quill.root.innerHTML;            
            alert('messageContent = '+messageContent);

            var parentId = document.getElementById('parent_message_id').value;

            if (!messageContent.trim()) {
                alert('Reply content is required.');
                return;
            }

            const formData = new FormData();
            formData.append('sender_id', <?php echo $loggedInUserId; ?>);
            formData.append('receiver_id', <?php echo $receiverId; ?>);
            formData.append('message', messageContent);
            formData.append('parent_message_id', parentId);         

            fetch('sendmessage.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                if (data.includes("Message sent")) {
                    document.getElementById('messageForm').reset();
                    quill.root.innerHTML='';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
            location.reload();
        }
</script>
</body>
</html>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <link rel="stylesheet" href="/FYP2025/SPAMS/client/css/Chat.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="contentBox">
            <div class="chatBox">
                <div class="titleBar">
                    <h1>Chat</h1>
                </div>

                <div class="divider">
                    <div class="chatList">
                        <ul class="chatSelection">
                            <li id="currentChat">
                                <a href="">
                                    <span>Assignment Group 1</span>
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    <span>Assignment Group 2</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="chatHalf">
                        <div class="chatTitle">
                            <p class="title">Assignment Group 1</p>
                        </div>

                        <div class="chatHistory">
                            <div class="userMessage">
                                <span class="userName">User Name</span>
                                <span class="userMsgContent">Testing 1 2 3 and 4</span>
                            </div>

                            <div class="otherMessage">
                                <span class="otherName">User Name</span>
                                <span class="otherMsgContent">Testing 1 2 3 and 4</span>
                            </div>

                            <?php
                            $userNames = ['Alice', 'Ben', 'Chloe'];
                            $messages = [
                                "Hey there!",
                                "Did you finish the assignment?",
                                "I'll check it tonight.",
                                "We should meet up to discuss this.",
                                "Great job!",
                                "Need help?",
                                "Almost done.",
                                "Let's submit by tonight.",
                                "I added my part to the doc.",
                                "See you later!",
                            ];

                            $chat = [];

                            // Generate 30 messages, randomly choosing user or other
                            for ($i = 0; $i < 30; $i++) {
                                $isUser = rand(0, 1) === 0; // 0 = userMessage, 1 = otherMessage
                                $name = $userNames[array_rand($userNames)];
                                $text = $messages[array_rand($messages)];

                                $chat[] = [
                                    'type' => $isUser ? 'userMessage' : 'otherMessage',
                                    'name' => $name,
                                    'text' => $text,
                                ];
                            }

                            // Shuffle messages to mix order
                            shuffle($chat);

                            // Output chat
                            foreach ($chat as $msg) {
                                echo "<div class=\"{$msg['type']}\">";
                                echo "<span class=\"" . ($msg['type'] === 'userMessage' ? 'userName' : 'otherName') . "\">{$msg['name']}</span>";
                                echo "<span class=\"" . ($msg['type'] === 'userMessage' ? 'userMsgContent' : 'otherMsgContent') . "\">{$msg['text']}</span>";
                                echo "</div>";
                            }
                            ?>
                        </div>

                        <form class="messageForm">
                                <div class="sendBar">                                   
                                    <textarea id="newMessage" name="newMessage" placeholder="Aa"></textarea>
                                    <button id="sendMessage" type="submit">Send</button>
                                </div>
                        </form>

                    </div>
                </div>

            </div>


        </div>

    </div>

    <script src="/FYP2025/SPAMS/client/js/Chat.js" defer></script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
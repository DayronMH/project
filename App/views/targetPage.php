<?php

function setTargetMessage($type, $message) {
    $_SESSION[$type] = $message;
}
?>
<div class="message-container">
    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error-message">
            <?php 
                echo htmlspecialchars($_SESSION['error']); 
                unset($_SESSION['error']); 
            ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success-message">
            <?php 
                echo htmlspecialchars($_SESSION['success']); 
                unset($_SESSION['success']); 
            ?>
        </div>
    <?php endif; ?>
</div>
<style>
.message-container {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
    width: 90%;
    max-width: 500px;
}

.message {
    padding: 15px 20px;
    border-radius: 15px;
    margin-bottom: 10px;
    text-align: center;
    font-weight: 500;
    animation: slideDown 0.5s ease-out;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
}
.success-message, .error-message {
    padding: 15px;
    border-radius: 8px;
    margin-top: 20px;
    font-size: 1em;
    text-align: center;
    position: relative;
    animation: slideIn 0.3s ease-in-out;
    font-weight: 500;
}
.success-message {
    background-color: rgba(46, 204, 113, 0.2);
    color: #27ae60;
    border: 2px solid #2ecc71;
    box-shadow: 0 4px 6px rgba(46, 204, 113, 0.1);
}
.error-message {
    background-color: rgba(231, 76, 60, 0.2);
    color: #c0392b;
    border: 2px solid #e74c3c;
    box-shadow: 0 4px 6px rgba(231, 76, 60, 0.1);
}

@keyframes slideDown {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
.fade-out {
    animation: fadeOut 0.5s ease-in forwards;
}
@keyframes fadeOut {
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(-20px);
    }
}

@media (max-width: 768px) {
    .message-container {
        width: 95%;
        top: 10px;
    }
    
    .message {
        padding: 12px 15px;
        font-size: 0.9em;
    }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messages = document.querySelectorAll('.message');
    
    messages.forEach(message => {

        setTimeout(() => {
            message.classList.add('fade-out');
        }, 2500);
        

        setTimeout(() => {
            message.remove();
        }, 3000);
        

        message.addEventListener('click', function() {
            this.classList.add('fade-out');
            setTimeout(() => {
                this.remove();
            }, 500);
        });
    });
});
</script>
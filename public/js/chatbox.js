document.addEventListener('DOMContentLoaded', function () {
    const chatToggle = document.getElementById('chat-toggle');
    const chatbox = document.getElementById('chatbox');
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');
    const chatMessages = document.getElementById('chatbox-messages');

    if (!chatToggle || !chatbox || !chatInput || !chatSend || !chatMessages) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function appendMessage(content, fromAdmin = false) {
        const div = document.createElement('div');
        div.className = fromAdmin ? 'mb-2 text-start' : 'mb-2 text-end';

        div.innerHTML = `
            <span style="
                display:inline-block;
                max-width:80%;
                padding:8px 12px;
                border-radius:12px;
                background:${fromAdmin ? '#f1f1f1' : '#f8c8dc'};
                color:#333;
            ">
                ${content}
            </span>
        `;

        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function clearWelcomeIfNeeded() {
        const firstP = chatMessages.querySelector('p');
        if (firstP) {
            firstP.remove();
        }
    }

    function sendMessage(message) {
        if (!message.trim()) return;

        clearWelcomeIfNeeded();
        appendMessage(message, false);

        fetch('/chatbox/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                name: 'Khách hàng',
                phone: '',
                content: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.reply) {
                appendMessage(data.reply, true);
            }
        })
        .catch(error => {
            console.error(error);
            appendMessage('Có lỗi xảy ra, vui lòng thử lại sau.', true);
        });

        chatInput.value = '';
    }

    function loadMessages() {
        fetch('/chatbox/messages', {
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(messages => {
            if (!Array.isArray(messages) || messages.length === 0) {
                return;
            }

            chatMessages.innerHTML = '';

            messages.forEach(msg => {
                appendMessage(msg.content, !!msg.from_admin);
            });
        })
        .catch(error => {
            console.error('Load messages error:', error);
        });
    }

    chatToggle.addEventListener('click', function () {
        chatbox.classList.toggle('show');

        if (chatbox.classList.contains('show')) {
            loadMessages();
        }
    });

    chatSend.addEventListener('click', function () {
        sendMessage(chatInput.value);
    });

    chatInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendMessage(chatInput.value);
        }
    });

    document.querySelectorAll('.faq-btn').forEach(button => {
        button.addEventListener('click', function () {
            const question = this.getAttribute('data-question');
            sendMessage(question);
        });
    });
});
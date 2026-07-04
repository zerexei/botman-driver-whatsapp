const chatForm = document.getElementById("chat-form");
const chat = document.getElementById("chat");
const input = document.getElementById("message");

// Suggestion action chips
document.querySelectorAll(".chip-btn").forEach(button => {
    button.addEventListener("click", () => {
        const text = button.getAttribute("data-message");
        if (text) {
            sendMessage(text);
        }
    });
});

chatForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const text = input.value.trim();
    if (!text) return;
    sendMessage(text);
    input.value = "";
});

async function sendMessage(text) {
    // Add user message to UI
    appendUserMessage(text);
    
    // Show typing/loading indicator
    const typingIndicator = showTypingIndicator();
    
    try {
        const response = await fetch("server.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
                driver: "web",
                userId: "user1",
                message: text,
            }),
        });

        // Remove typing indicator
        typingIndicator.remove();

        if (!response.ok) {
            appendBotMessage("⚠️ Server error occurred. Please try again.");
            return;
        }

        const payload = await response.json();

        if (payload?.messages?.length) {
            payload.messages.forEach(msg => {
                if (msg.text) {
                    appendBotMessage(msg.text);
                }
            });
        } else {
            appendBotMessage("😅 I received an empty response. Check if backend is listening.");
        }
    } catch (err) {
        typingIndicator.remove();
        appendBotMessage("❌ Failed to connect to server. Ensure PHP server is running.");
    }
}

function appendUserMessage(text) {
    const wrapper = document.createElement("div");
    wrapper.className = "flex justify-end animate-message";
    wrapper.innerHTML = `
        <div class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white p-4 rounded-2xl rounded-br-none max-w-md shadow-md text-[15px] leading-relaxed">
            ${escapeHTML(text)}
        </div>
    `;
    chat.appendChild(wrapper);
    scrollToBottom();
}

function appendBotMessage(text) {
    const wrapper = document.createElement("div");
    wrapper.className = "flex justify-start animate-message";
    wrapper.innerHTML = `
        <div class="flex gap-3 max-w-lg">
            <div class="w-8 h-8 rounded-xl bg-slate-800 flex items-center justify-center text-sm flex-shrink-0 self-end">🤖</div>
            <div class="glass-bubble-bot text-slate-100 p-4 rounded-2xl rounded-bl-none shadow-sm leading-relaxed text-[15px]">
                ${escapeHTML(text)}
            </div>
        </div>
    `;
    chat.appendChild(wrapper);
    scrollToBottom();
}

function showTypingIndicator() {
    const wrapper = document.createElement("div");
    wrapper.className = "flex justify-start animate-message";
    wrapper.id = "typing-indicator";
    wrapper.innerHTML = `
        <div class="flex gap-3 max-w-lg">
            <div class="w-8 h-8 rounded-xl bg-slate-800 flex items-center justify-center text-sm flex-shrink-0 self-end">🤖</div>
            <div class="glass-bubble-bot text-slate-400 px-5 py-4 rounded-2xl rounded-bl-none shadow-sm flex items-center gap-1">
                <span class="w-2 h-2 bg-slate-400 rounded-full typing-dot"></span>
                <span class="w-2 h-2 bg-slate-400 rounded-full typing-dot"></span>
                <span class="w-2 h-2 bg-slate-400 rounded-full typing-dot"></span>
            </div>
        </div>
    `;
    chat.appendChild(wrapper);
    scrollToBottom();
    return wrapper;
}

function scrollToBottom() {
    chat.scrollTop = chat.scrollHeight;
}

function escapeHTML(str) {
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

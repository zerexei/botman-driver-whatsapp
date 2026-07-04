<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antigravity BotMan Client 🤖</title>

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top right, #1e1b4b, #0f172a 60%);
        }

        .heading-font {
            font-family: 'Outfit', sans-serif;
        }

        /* Frosted glass effect */
        .glass-panel {
            background: rgba(30, 41, 59, 0.45);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glass-bubble-bot {
            background: rgba(51, 65, 85, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(8px);
        }

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-message {
            animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Typing Dots Animation */
        @keyframes bounceDot {
            0%, 100% {
                transform: translateY(0);
                opacity: 0.4;
            }
            50% {
                transform: translateY(-4px);
                opacity: 1;
            }
        }

        .typing-dot {
            animation: bounceDot 1.4s infinite ease-in-out;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        /* Customized scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 9999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body class="text-gray-100 h-screen flex flex-col items-center justify-center p-4 md:p-8 antialiased">

    <!-- Main Container -->
    <div class="flex flex-col w-full max-w-4xl h-[90vh] glass-panel rounded-3xl shadow-2xl overflow-hidden relative">

        <!-- Decorative Glow Blur Elements -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-purple-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header -->
        <div class="relative z-10 px-6 py-4 bg-slate-900/60 border-b border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <!-- Bot Avatar with Online Pulse -->
                <div class="relative">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-violet-600 to-indigo-600 flex items-center justify-center shadow-md">
                        <span class="text-lg">🤖</span>
                    </div>
                    <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-emerald-400 ring-2 ring-slate-900 animate-pulse"></span>
                </div>
                <div>
                    <h1 class="heading-font font-bold text-lg leading-tight tracking-wide bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-transparent">
                        BotMan Assistant
                    </h1>
                    <span class="text-xs text-slate-400 font-medium">Always online & eager to help</span>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-xs bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 px-2.5 py-1 rounded-full font-semibold">Web Driver v1.5</span>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="chat" class="relative z-10 flex-1 overflow-y-auto p-6 space-y-6">
            
            <!-- Welcome message -->
            <div class="flex justify-start animate-message">
                <div class="flex gap-3 max-w-lg">
                    <div class="w-8 h-8 rounded-xl bg-slate-800 flex items-center justify-center text-sm flex-shrink-0 self-end">🤖</div>
                    <div class="glass-bubble-bot text-slate-100 p-4 rounded-2xl rounded-bl-none shadow-sm leading-relaxed text-[15px]">
                        Welcome to the modernized BotMan Driver client! 👋 How can I help you today?
                    </div>
                </div>
            </div>

        </div>

        <!-- Suggestion Action Chips -->
        <div class="relative z-10 px-6 py-2 flex flex-wrap gap-2 items-center bg-slate-900/20 border-t border-white/5">
            <span class="text-xs text-slate-500 font-medium mr-1 uppercase tracking-wider">Suggestions:</span>
            <button type="button" class="chip-btn text-xs bg-slate-800/60 hover:bg-slate-700/80 text-slate-300 border border-white/5 px-3 py-1.5 rounded-full font-medium transition duration-200 active:scale-95 cursor-pointer" data-message="hello">
                👋 Say hello
            </button>
            <button type="button" class="chip-btn text-xs bg-slate-800/60 hover:bg-slate-700/80 text-slate-300 border border-white/5 px-3 py-1.5 rounded-full font-medium transition duration-200 active:scale-95 cursor-pointer" data-message="what is your name">
                🤖 Ask name
            </button>
            <button type="button" class="chip-btn text-xs bg-slate-800/60 hover:bg-slate-700/80 text-slate-300 border border-white/5 px-3 py-1.5 rounded-full font-medium transition duration-200 active:scale-95 cursor-pointer" data-message="what time is it">
                ⏰ Ask time
            </button>
            <button type="button" class="chip-btn text-xs bg-slate-800/60 hover:bg-slate-700/80 text-slate-300 border border-white/5 px-3 py-1.5 rounded-full font-medium transition duration-200 active:scale-95 cursor-pointer" data-message="tell me a joke">
                😂 Tell joke
            </button>
            <button type="button" class="chip-btn text-xs bg-slate-800/60 hover:bg-slate-700/80 text-slate-300 border border-white/5 px-3 py-1.5 rounded-full font-medium transition duration-200 active:scale-95 cursor-pointer" data-message="help">
                🧭 Help
            </button>
        </div>

        <!-- Input Area Form -->
        <form id="chat-form" class="relative z-10 flex items-center gap-3 p-4 md:p-6 bg-slate-900/60 border-t border-white/5">
            <input
                id="message"
                type="text"
                placeholder="Type your message here..."
                class="flex-1 bg-slate-950/60 text-slate-100 placeholder-slate-500 border border-white/5 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all duration-200 text-[15px]"
                autocomplete="off" />
            <button
                type="submit"
                class="bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white font-semibold px-6 py-4 rounded-2xl shadow-lg shadow-indigo-600/20 hover:shadow-indigo-600/30 transition-all duration-200 active:scale-98 flex items-center justify-center gap-2 cursor-pointer">
                <span>Send</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </button>
        </form>
    </div>

    <!-- Client Script -->
    <script src="index.js" defer></script>
</body>

</html>
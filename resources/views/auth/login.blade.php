<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>লগইন | সানা ফিশারিজ - মাছের ছানা ও পোনা কেনাবেচা</title>

    <!-- Google Fonts: Inter & Hind Siliguri for Bengali -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (via CDN fallback ensuring instant render without build step) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Hind Siliguri"', '"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Plus Jakarta Sans"', '"Hind Siliguri"', 'sans-serif'],
                    },
                    colors: {
                        aqua: {
                            50: '#ecfeff',
                            100: '#cffafe',
                            200: '#a5f3fc',
                            300: '#67e8f9',
                            400: '#22d3ee',
                            500: '#06b6d4',
                            600: '#0891b2',
                            700: '#0e7490',
                            800: '#155e75',
                            900: '#164e63',
                            950: '#083344',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Hind Siliguri', 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: radial-gradient(circle at 80% 20%, #0d4a68 0%, #05263b 35%, #021724 70%, #010e17 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient light ray piercing through water */
        .sun-ray {
            position: absolute;
            top: -150px;
            right: 10%;
            width: 450px;
            height: 900px;
            background: linear-gradient(135deg, rgba(34, 211, 238, 0.18) 0%, rgba(6, 182, 212, 0.05) 50%, transparent 80%);
            transform: rotate(-25deg);
            filter: blur(40px);
            pointer-events: none;
            z-index: 1;
            animation: rayShift 8s ease-in-out infinite alternate;
        }

        .sun-ray-2 {
            position: absolute;
            top: -100px;
            left: 5%;
            width: 350px;
            height: 700px;
            background: linear-gradient(135deg, rgba(20, 184, 166, 0.12) 0%, rgba(13, 148, 136, 0.03) 60%, transparent 80%);
            transform: rotate(20deg);
            filter: blur(50px);
            pointer-events: none;
            z-index: 1;
            animation: rayShift 10s ease-in-out infinite alternate-reverse;
        }

        @keyframes rayShift {
            0% { opacity: 0.7; transform: rotate(-25deg) translateY(0); }
            100% { opacity: 1; transform: rotate(-22deg) translateY(20px); }
        }

        /* Animated Floating Bubbles */
        .bubble-container {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 1;
        }

        .bubble {
            position: absolute;
            bottom: -50px;
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.85), rgba(34, 211, 238, 0.25) 45%, rgba(6, 182, 212, 0.08) 80%);
            border: 1px solid rgba(255, 255, 255, 0.45);
            border-radius: 50%;
            box-shadow: 0 0 12px rgba(34, 211, 238, 0.35), inset 0 0 6px rgba(255, 255, 255, 0.6);
            animation: floatUp linear infinite;
        }

        @keyframes floatUp {
            0% {
                transform: translateY(0) translateX(0) scale(0.8);
                opacity: 0;
            }
            10% {
                opacity: 0.85;
            }
            85% {
                opacity: 0.85;
            }
            100% {
                transform: translateY(-110vh) translateX(30px) scale(1.15);
                opacity: 0;
            }
        }

        /* Swimming fish fry animation */
        .fish-track {
            position: absolute;
            width: 100%;
            pointer-events: none;
            z-index: 2;
        }

        .swimming-fish {
            position: absolute;
            animation: swimRight linear infinite;
        }

        .swimming-fish-reverse {
            position: absolute;
            animation: swimLeft linear infinite;
        }

        @keyframes swimRight {
            0% {
                transform: translateX(-150px) translateY(0) scale(0.85);
                opacity: 0;
            }
            5% {
                opacity: 0.75;
            }
            50% {
                transform: translateX(50vw) translateY(-18px) scale(0.9);
            }
            95% {
                opacity: 0.75;
            }
            100% {
                transform: translateX(calc(100vw + 150px)) translateY(10px) scale(0.85);
                opacity: 0;
            }
        }

        @keyframes swimLeft {
            0% {
                transform: translateX(calc(100vw + 150px)) translateY(0) scaleX(-1) scale(0.75);
                opacity: 0;
            }
            5% {
                opacity: 0.65;
            }
            50% {
                transform: translateX(45vw) translateY(22px) scaleX(-1) scale(0.8);
            }
            95% {
                opacity: 0.65;
            }
            100% {
                transform: translateX(-150px) translateY(-12px) scaleX(-1) scale(0.75);
                opacity: 0;
            }
        }

        .fish-tail {
            transform-origin: 20% 50%;
            animation: wiggleTail 0.4s ease-in-out infinite alternate;
        }

        @keyframes wiggleTail {
            0% { transform: rotate(-10deg); }
            100% { transform: rotate(10deg); }
        }

        /* Glassmorphism card */
        .glass-card {
            background: rgba(10, 37, 56, 0.72);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(56, 189, 248, 0.22);
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.6), 0 0 30px rgba(14, 165, 233, 0.12);
        }

        .glass-inner {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Water wave animation at footer */
        .water-wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 200%;
            height: 110px;
            background-repeat: repeat-x;
            opacity: 0.25;
            pointer-events: none;
            z-index: 1;
            animation: waveFlow 18s linear infinite;
        }

        @keyframes waveFlow {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* Input field glow */
        .input-glow:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.22), 0 0 20px rgba(56, 189, 248, 0.2);
        }

        /* Button ripple & sheen */
        .btn-aquatic {
            background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 40%, #0d9488 100%);
            box-shadow: 0 10px 25px -5px rgba(2, 132, 199, 0.45), 0 0 20px rgba(14, 165, 233, 0.25);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-aquatic:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(2, 132, 199, 0.6), 0 0 30px rgba(14, 165, 233, 0.4);
            background: linear-gradient(135deg, #0369a1 0%, #0284c7 40%, #0f766e 100%);
        }

        .btn-aquatic::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -60%;
            width: 40%;
            height: 200%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
            transform: rotate(25deg);
            animation: btnShine 4s infinite;
        }

        @keyframes btnShine {
            0%, 70% { left: -60%; }
            100% { left: 140%; }
        }

        /* Pulse glow */
        .pulse-aqua {
            animation: pulseGlow 2.5s infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(34, 211, 238, 0.4); }
            50% { box-shadow: 0 0 0 12px rgba(34, 211, 238, 0); }
        }

        /* Image hover shine */
        .image-frame {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            border: 2px solid rgba(56, 189, 248, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), inset 0 0 25px rgba(56, 189, 248, 0.2);
        }

        .image-frame img {
            transition: transform 0.6s ease;
        }

        .image-frame:hover img {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="flex items-center justify-center p-4 sm:p-6 lg:p-10 relative">

    <!-- Ambient light rays -->
    <div class="sun-ray"></div>
    <div class="sun-ray-2"></div>

    <!-- Rising Water Bubbles -->
    <div class="bubble-container">
        <div class="bubble" style="width: 26px; height: 26px; left: 8%; animation-duration: 9s; animation-delay: 0s;"></div>
        <div class="bubble" style="width: 14px; height: 14px; left: 16%; animation-duration: 7s; animation-delay: 2.5s;"></div>
        <div class="bubble" style="width: 34px; height: 34px; left: 27%; animation-duration: 11s; animation-delay: 1s;"></div>
        <div class="bubble" style="width: 18px; height: 18px; left: 38%; animation-duration: 8s; animation-delay: 3.8s;"></div>
        <div class="bubble" style="width: 22px; height: 22px; left: 49%; animation-duration: 10s; animation-delay: 0.5s;"></div>
        <div class="bubble" style="width: 12px; height: 12px; left: 58%; animation-duration: 6.5s; animation-delay: 4.2s;"></div>
        <div class="bubble" style="width: 30px; height: 30px; left: 69%; animation-duration: 12s; animation-delay: 1.8s;"></div>
        <div class="bubble" style="width: 16px; height: 16px; left: 78%; animation-duration: 7.5s; animation-delay: 3s;"></div>
        <div class="bubble" style="width: 24px; height: 24px; left: 88%; animation-duration: 9.5s; animation-delay: 2s;"></div>
        <div class="bubble" style="width: 10px; height: 10px; left: 94%; animation-duration: 6s; animation-delay: 4.8s;"></div>
    </div>

    <!-- Animated Swimming Fish Fry (School of fry) -->
    <div class="fish-track" style="top: 14%;">
        <div class="swimming-fish" style="animation-duration: 14s; animation-delay: 0s;">
            <svg width="42" height="18" viewBox="0 0 50 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Fish Fry Silhouette with glowing aquatic color -->
                <path d="M42 11C40 7 32 3 22 3C12 3 4 8 2 11C4 14 12 19 22 19C32 19 40 15 42 11Z" fill="url(#fishGrad1)" opacity="0.9"/>
                <path class="fish-tail" d="M41 11L49 4V18L41 11Z" fill="url(#fishGrad1)" opacity="0.9"/>
                <circle cx="9" cy="9.5" r="1.5" fill="#ffffff"/>
                <circle cx="9.3" cy="9.5" r="0.8" fill="#0c4a6e"/>
                <defs>
                    <linearGradient id="fishGrad1" x1="0" y1="11" x2="50" y2="11" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#38bdf8"/>
                        <stop offset="0.6" stop-color="#2dd4bf"/>
                        <stop offset="1" stop-color="#f0fdfa"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>
        <div class="swimming-fish" style="animation-duration: 13.5s; animation-delay: 0.6s; top: 18px;">
            <svg width="34" height="15" viewBox="0 0 50 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M42 11C40 7 32 3 22 3C12 3 4 8 2 11C4 14 12 19 22 19C32 19 40 15 42 11Z" fill="url(#fishGrad1)" opacity="0.8"/>
                <path class="fish-tail" d="M41 11L49 4V18L41 11Z" fill="url(#fishGrad1)" opacity="0.8"/>
                <circle cx="9" cy="9.5" r="1.5" fill="#ffffff"/>
            </svg>
        </div>
    </div>

    <div class="fish-track" style="top: 68%;">
        <div class="swimming-fish-reverse" style="animation-duration: 16s; animation-delay: 2s;">
            <svg width="48" height="20" viewBox="0 0 50 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M42 11C40 7 32 3 22 3C12 3 4 8 2 11C4 14 12 19 22 19C32 19 40 15 42 11Z" fill="url(#fishGrad2)" opacity="0.85"/>
                <path class="fish-tail" d="M41 11L49 4V18L41 11Z" fill="url(#fishGrad2)" opacity="0.85"/>
                <circle cx="9" cy="9.5" r="1.5" fill="#ffffff"/>
                <circle cx="9.3" cy="9.5" r="0.8" fill="#0369a1"/>
                <defs>
                    <linearGradient id="fishGrad2" x1="0" y1="11" x2="50" y2="11" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#22d3ee"/>
                        <stop offset="0.5" stop-color="#38bdf8"/>
                        <stop offset="1" stop-color="#a5f3fc"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>
        <div class="swimming-fish-reverse" style="animation-duration: 15.2s; animation-delay: 2.8s; top: -16px;">
            <svg width="30" height="13" viewBox="0 0 50 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M42 11C40 7 32 3 22 3C12 3 4 8 2 11C4 14 12 19 22 19C32 19 40 15 42 11Z" fill="url(#fishGrad2)" opacity="0.75"/>
                <path class="fish-tail" d="M41 11L49 4V18L41 11Z" fill="url(#fishGrad2)" opacity="0.75"/>
                <circle cx="9" cy="9.5" r="1.5" fill="#ffffff"/>
            </svg>
        </div>
    </div>

    <!-- MAIN APP CONTAINER -->
    <div class="w-full max-w-6xl mx-auto z-10 my-auto">
        
        <!-- Top branding bar for mobile -->
        <div class="flex items-center justify-between mb-4 lg:hidden px-2">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-500 to-sky-400 flex items-center justify-center text-white shadow-lg pulse-aqua">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-white font-bold text-lg leading-tight">সানা ফিশারিজ</h1>
                    <p class="text-cyan-300 text-xs">মাছের ছানা ও পোনা কেনাবেচা</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                লাইভ পোর্টাল
            </span>
        </div>

        <!-- Glassmorphism Card Container -->
        <div class="glass-card rounded-3xl overflow-hidden shadow-2xl grid grid-cols-1 lg:grid-cols-12 border border-sky-400/20">

            <!-- LEFT PANEL: Aquaculture Showcase, Imagery & Visuals (7 cols on lg) -->
            <div class="lg:col-span-7 p-6 sm:p-8 lg:p-10 flex flex-col justify-between relative overflow-hidden bg-gradient-to-br from-sky-950/60 via-slate-900/60 to-cyan-950/40">
                
                <!-- Water shine overlay -->
                <div class="absolute -top-24 -left-24 w-72 h-72 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div>
                    <!-- Brand Tagline -->
                    <div class="flex items-center gap-2 mb-4">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.2 rounded-full text-xs font-semibold bg-cyan-500/15 text-cyan-200 border border-cyan-400/30 backdrop-blur-md">
                            <span class="text-base">🐟</span>
                            <span>হ্যাচারি ও খামারি ট্রেডিং প্ল্যাটফর্ম</span>
                        </span>
                        <span class="hidden sm:inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-400/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            নিরাপদ লেনদেন
                        </span>
                    </div>

                    <!-- Main Catchy Title -->
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight mb-3">
                        মাছের ছানা ও পোনা <br class="hidden sm:block"/>
                        <span class="bg-gradient-to-r from-cyan-300 via-sky-300 to-teal-200 bg-clip-text text-transparent">কেনাবেচার আধুনিক সফটওয়্যার</span>
                    </h2>

                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed mb-6 font-normal">
                        রুই, কাতলা, মৃগেল, পাঙ্গাশ, তেলাপিয়া ও শিং-মাগুরসহ সব জাতের উন্নত মানের পোনা ও ছানা ক্রয়-বিক্রয়, হ্যাচারি চালান ও ডেলিভারি হিসাবের বিশ্বস্ত পোর্টাল।
                    </p>

                    <!-- Real Photo Showcase with Animated Glow -->
                    <div class="image-frame mb-6 group">
                        <img 
                            src="{{ asset('images/fish-fry-banner.jpg') }}" 
                            alt="মাছের ছানা ও পোনা" 
                            class="w-full h-48 sm:h-56 lg:h-64 object-cover"
                            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1200&q=80';"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent flex items-end p-4">
                            <div class="flex items-center justify-between w-full">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span class="text-xs sm:text-sm font-semibold text-white tracking-wide drop-shadow">সরাসরি হ্যাচারি থেকে জীবন্ত পোনা সরবরাহ</span>
                                </div>
                                <span class="text-xs px-2.5 py-1 rounded-md bg-cyan-500/30 text-cyan-200 border border-cyan-400/40 backdrop-blur-sm">
                                    অক্সিজেন প্যাক রেডি
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Feature Badges -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3 text-xs sm:text-sm">
                        <div class="p-3 rounded-xl bg-slate-800/40 border border-cyan-500/15 flex items-center gap-2 text-slate-200">
                            <div class="w-7 h-7 rounded-lg bg-cyan-500/20 text-cyan-300 flex items-center justify-center shrink-0">
                                📊
                            </div>
                            <span class="font-medium">সাইজ ও কাউন্ট হিসাব</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-800/40 border border-cyan-500/15 flex items-center gap-2 text-slate-200">
                            <div class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center shrink-0">
                                🚚
                            </div>
                            <span class="font-medium">লাইভ চালান ট্র্যাকিং</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-800/40 border border-cyan-500/15 flex items-center gap-2 text-slate-200 col-span-2 sm:col-span-1">
                            <div class="w-7 h-7 rounded-lg bg-sky-500/20 text-sky-300 flex items-center justify-center shrink-0">
                                🧾
                            </div>
                            <span class="font-medium">দ্রুত ডিজিটাল ভাউচার</span>
                        </div>
                    </div>
                </div>

                <!-- Bottom info -->
                <div class="mt-6 pt-4 border-t border-slate-700/40 flex items-center justify-between text-xs text-slate-400">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-cyan-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        প্রমাণিত গুণমান ও সুস্থ পোনার নিশ্চয়তা
                    </span>
                    <span class="text-cyan-300/80 font-medium hidden sm:inline">সানা প্ল্যাটফর্ম v২.০</span>
                </div>
            </div>


            <!-- RIGHT PANEL: Authentication / Login Form (5 cols on lg) -->
            <div class="lg:col-span-5 p-6 sm:p-8 lg:p-10 flex flex-col justify-center bg-slate-900/85 relative">
                
                <!-- Water ripple logo emblem -->
                <div class="flex items-center gap-3.5 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyan-500 via-sky-500 to-teal-400 flex items-center justify-center text-white shadow-xl shadow-cyan-500/30 pulse-aqua">
                        <!-- Custom Fish Icon -->
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6.5 12c.94-3.46 4.94-6 8.5-6 3.56 0 6.06 2.54 7 6-.94 3.46-3.44 6-7 6s-7.56-2.54-8.5-6Z"/>
                            <path d="M18 12v.5"/>
                            <path d="M16 10h.01"/>
                            <path d="M2 16l4.5-4L2 8"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl sm:text-2xl font-bold text-white tracking-tight">লগইন পোর্টাল</h3>
                        <p class="text-xs sm:text-sm text-cyan-300 font-medium">অ্যাকাউন্টে প্রবেশ করতে তথ্য দিন</p>
                    </div>
                </div>

                <!-- Session Status Message -->
                @if (session('status'))
                    <div class="mb-4 p-3.5 rounded-xl bg-cyan-500/20 border border-cyan-400/40 text-cyan-200 text-sm flex items-center gap-2">
                        <svg class="w-5 h-5 shrink-0 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="mb-4 p-3.5 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-200 text-sm">
                        <div class="flex items-center gap-2 font-semibold mb-1 text-rose-300">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            তথ্য সঠিকভাবে পূরণ করুন:
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-200/90 pl-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs sm:text-sm font-semibold text-slate-200 mb-1.5">
                            ইমেইল এড্রেস <span class="text-cyan-400 font-normal">(Email Address)</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5 text-cyan-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus 
                                autocomplete="username"
                                placeholder="name@domain.com"
                                class="input-glow w-full pl-11 pr-4 py-3 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white placeholder-slate-500 text-sm transition duration-200"
                            />
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs sm:text-sm font-semibold text-slate-200">
                                পাসওয়ার্ড <span class="text-cyan-400 font-normal">(Password)</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-medium transition duration-150 underline-offset-2 hover:underline">
                                    পাসওয়ার্ড ভুলে গেছেন?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5 text-cyan-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="input-glow w-full pl-11 pr-11 py-3 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white placeholder-slate-500 text-sm transition duration-200"
                            />
                            <!-- Toggle Password Visibility -->
                            <button 
                                type="button" 
                                id="togglePasswordBtn" 
                                onclick="togglePasswordVisibility()"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-cyan-300 transition"
                                title="পাসওয়ার্ড দেখুন/লুকান"
                            >
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eyeSlashIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                            <input 
                                id="remember_me" 
                                type="checkbox" 
                                name="remember" 
                                class="w-4 h-4 rounded bg-slate-800 border-slate-700 text-cyan-500 focus:ring-cyan-400 focus:ring-offset-slate-900 cursor-pointer"
                            >
                            <span class="ms-2.5 text-xs sm:text-sm text-slate-300 font-medium">আমাকে মনে রাখুন (Remember me)</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            data-loading-text="লগইন হচ্ছে..."
                            class="btn-submit-with-spinner btn-aquatic w-full py-3.5 px-6 rounded-xl text-white font-bold text-sm sm:text-base flex items-center justify-center gap-2 cursor-pointer shadow-lg tracking-wide disabled:opacity-80 disabled:cursor-not-allowed"
                        >
                            <span class="btn-spinner hidden inline-flex items-center mr-1">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            <span class="btn-content inline-flex items-center gap-2">
                                <span>লগইন করুন</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </span>
                        </button>
                    </div>

                    <!-- Security Badge Notice -->
                    <div class="pt-4 text-center">
                        <p class="text-xs text-slate-400 flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-400 inline" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>নিরাপদ ও এনক্রিপ্টেড হ্যাচারি ম্যানেজমেন্ট সিস্টেম</span>
                        </p>
                    </div>
                </form>

            </div>

        </div>

        <!-- Footer Credits -->
        <div class="text-center mt-6 text-xs text-cyan-200/60 font-medium">
            &copy; {{ date('Y') }} সানা ফিশারিজ ও হ্যাচারি ম্যানেজমেন্ট সফটওয়্যার • সর্বস্বত্ব সংরক্ষিত
        </div>

    </div>

    <!-- Interactive script for password view toggle -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeSlashIcon = document.getElementById('eyeSlashIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }
    </script>

    <!-- Global Submit Button Spinner Trait -->
    <x-submit-spinner-script />
</body>
</html>

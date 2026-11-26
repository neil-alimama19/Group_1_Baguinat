<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPAMAST SSG | Event Management System</title>

    <style>
        /* Same design as the dashboards and login page:
           dark purple gradient, purple accents, glass cards, Segoe UI font */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth; /* smooth jump for the #home / #features links */
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #2e1065, #1e1b4b);
            background-attachment: fixed;
            min-height: 100vh;
            color: white;
            line-height: 1.6;
        }

        /* ---------- Header ---------- */
        header {
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 18px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #c084fc;
        }

        .logo span {
            display: block;
            font-size: 11px;
            color: #94a3b8;
            letter-spacing: 2px;
        }

        nav {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        nav a {
            text-decoration: none;
            color: #d8b4fe;
            font-weight: 600;
            font-size: 14px;
            transition: color 0.2s;
        }

        nav a:hover {
            color: white;
        }

        nav .nav-button {
            background: #7c3aed;
            color: white;
            padding: 10px 17px;
            border-radius: 8px;
            transition: background 0.2s;
        }

        nav .nav-button:hover {
            background: #6d28d9;
        }

        /* ---------- Flash message (e.g. after logout) ---------- */
        .flash {
            margin: 20px 8% 0;
            padding: 14px 18px;
            border-radius: 10px;
            font-weight: bold;
            background: #dcfce7;
            color: #166534;
        }

        /* ---------- Hero ---------- */
        .hero {
            padding: 90px 8%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
        }

        .hero-content {
            max-width: 650px;
        }

        .tag {
            display: inline-block;
            background: rgba(192, 132, 252, 0.15);
            border: 1px solid rgba(192, 132, 252, 0.4);
            color: #d8b4fe;
            padding: 7px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        h1 {
            font-size: clamp(36px, 5vw, 58px);
            line-height: 1.12;
            margin-bottom: 20px;
        }

        h1 span {
            color: #c084fc;
        }

        .hero p {
            color: #cbd5e1;
            font-size: 17px;
            max-width: 560px;
            margin-bottom: 28px;
        }

        .buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .button {
            display: inline-block;
            padding: 13px 23px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.2s, transform 0.2s;
        }

        .button:hover {
            transform: translateY(-2px);
        }

        .primary {
            background: #7c3aed;
            color: white;
        }

        .primary:hover {
            background: #6d28d9;
        }

        .secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #d8b4fe;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .secondary:hover {
            background: rgba(255, 255, 255, 0.16);
            color: white;
        }

        /* Glass card, same look as the dashboard cards */
        .hero-card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 30px;
            border-radius: 20px;
            min-width: 260px;
            max-width: 310px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .hero-card .symbol {
            font-size: 45px;
            margin-bottom: 15px;
        }

        .hero-card h2 {
            font-size: 23px;
            margin-bottom: 10px;
            color: #e9d5ff;
        }

        .hero-card p {
            font-size: 15px;
            margin-bottom: 0;
        }

        /* ---------- Features ---------- */
        .features {
            padding: 75px 8%;
            text-align: center;
        }

        .section-label {
            color: #c084fc;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .features h2 {
            font-size: 34px;
            margin: 10px 0;
        }

        .section-description {
            color: #94a3b8;
            margin-bottom: 35px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
            text-align: left;
        }

        .feature {
            background: rgba(255, 255, 255, 0.05);
            padding: 25px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.2s, background 0.2s, border-color 0.2s;
        }

        .feature:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(192, 132, 252, 0.5);
        }

        .feature-icon {
            font-size: 30px;
            margin-bottom: 15px;
        }

        .feature h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #e9d5ff;
        }

        .feature p {
            color: #94a3b8;
            font-size: 14px;
        }

        /* ---------- Call to action ---------- */
        .cta {
            margin: 0 8% 70px;
            padding: 45px 25px;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: white;
            border-radius: 18px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .cta h2 {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .cta p {
            color: #ede9fe;
            margin-bottom: 22px;
        }

        /* On the purple banner the secondary button turns white so it stands out */
        .cta .secondary {
            background: white;
            color: #6d28d9;
            border: none;
        }

        .cta .secondary:hover {
            background: #ede9fe;
            color: #5b21b6;
        }

        /* ---------- Footer ---------- */
        footer {
            padding: 25px 8%;
            background: rgba(0, 0, 0, 0.2);
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 13px;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 900px) {
            .hero {
                flex-direction: column;
                align-items: flex-start;
                padding: 65px 6%;
            }

            .hero-card {
                max-width: 100%;
            }

            .feature-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 550px) {
            header {
                padding: 16px 5%;
                flex-wrap: wrap;
            }

            nav {
                flex-wrap: wrap;
            }

            .flash {
                margin: 16px 5% 0;
            }

            .hero {
                padding: 50px 5%;
            }

            .features {
                padding: 55px 5%;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .features h2 {
                font-size: 28px;
            }

            .cta {
                margin: 0 5% 45px;
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">
            SPAMAST SSG
            <span>EVENT MANAGEMENT SYSTEM</span>
        </div>

        <nav>
            <a href="#home">Home</a>
            <a href="#features">Features</a>
            @guest
                {{-- Not logged in: show Login and Register --}}
                <a href="{{ route('login') }}" class="nav-button">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endguest
            @auth
                {{-- Logged in: "main" sends the user to their own role dashboard --}}
                <a href="{{ route('main') }}" class="nav-button">Dashboard</a>
                <a href="{{ route('profile.edit') }}">My Profile</a>
            @endauth
        </nav>
    </header>

    {{-- Shows messages such as "You have been logged out." --}}
    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    <main>
        <section class="hero" id="home">
            <div class="hero-content">
                <span class="tag">STUDENT LEADERSHIP • CAMPUS EVENTS</span>

                <h1>
                    Every Event.<br>
                    Every Student.<br>
                    <span>One System.</span>
                </h1>

                <p>
                    Welcome to the SPAMAST SSG Event Management System.
                    A centralized platform designed to organize campus
                    events, manage student participation, monitor attendance,
                    and keep track of sanctions.
                </p>

                <div class="buttons">
                    @guest
                        <a href="{{ route('register') }}" class="button primary">
                            Get Started →
                        </a>
                    @endguest
                    @auth
                        <a href="{{ route('main') }}" class="button primary">
                            Go to Dashboard →
                        </a>
                    @endauth

                    <a href="#features" class="button secondary">
                        Explore Features
                    </a>
                </div>
            </div>

            <div class="hero-card">
                <div class="symbol">🎓</div>
                <h2>Organize. Participate. Lead.</h2>
                <p>
                    Bringing student organizations and campus activities
                    together through one organized digital platform.
                </p>
            </div>
        </section>

        <section class="features" id="features">
            <span class="section-label">What You Can Do</span>
            <h2>Everything in One Place</h2>
            <p class="section-description">
                Tools to support student affairs and event management.
            </p>

            <div class="feature-grid">
                <article class="feature">
                    <div class="feature-icon">👥</div>
                    <h3>Register Students</h3>
                    <p>
                        Organize student information and maintain
                        accessible registration records.
                    </p>
                </article>

                <article class="feature">
                    <div class="feature-icon">📅</div>
                    <h3>Manage Events</h3>
                    <p>
                        Keep campus activities organized with clear
                        event information and schedules.
                    </p>
                </article>

                <article class="feature">
                    <div class="feature-icon">✓</div>
                    <h3>Attendance</h3>
                    <p>
                        Record and monitor student attendance during
                        school activities and events.
                    </p>
                </article>

                <article class="feature">
                    <div class="feature-icon">📋</div>
                    <h3>Sanctions</h3>
                    <p>
                        Maintain organized records of event-related
                        sanctions and student accountability.
                    </p>
                </article>
            </div>
        </section>

        <section class="cta" id="register">
            <h2>Be Part of Every Campus Event</h2>
            <p>
                Stay organized, stay informed, and stay involved.
            </p>

            @guest
                <a href="{{ route('register') }}" class="button secondary">
                    Register Now
                </a>
            @endguest
            @auth
                <a href="{{ route('main') }}" class="button secondary">
                    Go to Dashboard
                </a>
            @endauth
        </section>
    </main>

    <footer>
        <p>
            © <span id="year"></span> SPAMAST SSG Event Management System.
            All rights reserved.
        </p>
    </footer>

    <script>
        document.getElementById("year").textContent =
            new Date().getFullYear();
    </script>

</body>
</html>

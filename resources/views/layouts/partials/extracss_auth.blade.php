   <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        /* ================================
           BACKGROUND OPTIONS
           ================================ */
         /* Note: Only uncomment one of the options below. */
        /* OPTION 1: Background Color/Gradient */
        /* Uncomment below for color background */
        
        html, body {
            height: 100%;
            background: #121212;
            background-image: radial-gradient(ellipse 120% 80% at 50% -20%, rgba(212, 175, 55, 0.08) 0%, transparent 55%);
            min-height: 100vh;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .dropdown-menu {
            background-color: #1a1a1a !important;
            border: 1px solid #333 !important;
        }
        .dropdown-menu > li > a {
            color: #ddd !important;
        }
        .dropdown-menu > li > a:hover {
            background-color: #333 !important;
            color: #fff !important;
        }

        /* Modern Login Card */
        .modern-login-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 20px;
            width: 100%;
            flex-direction: row;
        }
        @media (max-width: 991px) {
            .modern-login-wrapper { flex-direction: column; gap: 30px; }
        }
        .modern-login-card {
            background: #161616;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 20px;
            padding: 40px 36px 36px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(212, 175, 55, 0.06);
            margin: 0 auto;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
        }
        .modern-login-card:hover {
            border-color: rgba(212, 175, 55, 0.22);
            box-shadow: 0 28px 72px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(212, 175, 55, 0.08);
        }
        
        .logo-container { text-align: center; margin-bottom: 24px; display: flex; justify-content: center; flex-direction: column; align-items: center; }
        .modern-logo {
            height: 120px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.3s;
            display: block;
            margin: 0 auto;
        }
        .modern-logo:hover { transform: scale(1.02); }
        .modern-login-icon {
            width: 92px;
            height: 92px;
            border-radius: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            color: #d4af37;
            background: linear-gradient(180deg, rgba(212, 175, 55, 0.18) 0%, rgba(212, 175, 55, 0.05) 100%);
            border: 1px solid rgba(212, 175, 55, 0.35);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        .modern-login-icon svg {
            width: 44px;
            height: 44px;
        }

        .modern-brand-tagline {
            margin: 0 0 8px;
            font-size: 13px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            text-align: center;
            line-height: 1.4;
        }
        .modern-brand-name {
            color: #e8c547;
            font-weight: 700;
            display: inline-block;
        }
        .modern-brand-by {
            color: rgba(255, 255, 255, 0.45);
            font-weight: 500;
            letter-spacing: 0.06em;
            text-transform: none;
        }

        .modern-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #fff;
            text-align: center;
            letter-spacing: 0.5px;
        }
        .modern-subtitle {
            font-size: 14px;
            color: #888;
            margin-bottom: 30px;
            text-align: center;
        }
        .modern-subtitle span { color: #d4af37; font-weight: 600; }
        
        /* Form Inputs */
        .modern-form-group { margin-bottom: 20px; }
        .modern-form-group label {
            display: block;
            margin-bottom: 8px;
            color: #ddd;
            font-weight: 500;
            font-size: 14px;
        }
        .modern-input-wrapper { position: relative; }
        .modern-input-wrapper .icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            width: 20px;
            height: 20px;
        }
        .modern-input {
            width: 100%;
            background: #0f0f0f;
            border: 1px solid #333;
            color: #fff;
            padding: 12px 16px 12px 42px;
            border-radius: 12px;
            font-size: 15px;
            height: 48px;
            outline: none;
            transition: all 0.2s;
            box-sizing: border-box;
        }
        .modern-input:focus {
            border-color: #d4af37;
            box-shadow: 0 0 0 1px #d4af37;
        }
        .modern-input-wrapper .btn-show-pass {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #888;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
        }
        .modern-input-wrapper .btn-show-pass:hover { color: #fff; }
        
        .modern-remember {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-size: 14px;
            color: #aaa;
            user-select: none;
            margin-top: 10px;
        }
        .modern-remember:hover { color: #ccc; }
        .modern-checkbox {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid #555;
            border-radius: 4px;
            background: transparent;
            cursor: pointer;
            position: relative;
            outline: none;
        }
        .modern-checkbox:checked {
            background: #d4af37;
            border-color: #d4af37;
        }
        .modern-checkbox:checked::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 1px;
            width: 5px;
            height: 10px;
            border: solid black;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        
        .modern-btn {
            width: 100%;
            background: linear-gradient(180deg, #c9a227 0%, #f5e6a8 45%, #b8860b 100%);
            color: #1a1204;
            border: none;
            border-radius: 9999px;
            height: 50px;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.02em;
            cursor: pointer;
            margin-top: 24px;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.28), inset 0 1px 0 rgba(255, 255, 255, 0.35);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }
        .modern-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(212, 175, 55, 0.38), inset 0 1px 0 rgba(255, 255, 255, 0.4);
            filter: brightness(1.05);
        }
        .modern-btn:active { transform: translateY(0); filter: brightness(0.98); }
        
        .modern-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #888;
        }
        .modern-footer a {
            color: #d4af37;
            font-weight: 600;
            text-decoration: none;
            margin-left: 5px;
        }
        .modern-footer a:hover { color: #fcf6ba; text-decoration: underline; }
        
        .forgot-pass {
            color: #d4af37;
            font-size: 13px;
            text-decoration: none;
            float: right;
            margin-top: 2px;
        }
        .forgot-pass:hover { color: #fcf6ba; text-decoration: underline; }
        .flex-between { display: flex; justify-content: space-between; align-items: center; width: 100%; }
        
        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
       
        
        /* OPTION 2: Background Image */
        /* Uncomment below for image background */
        /* html {
            height: 100%;
            background-image: url('{{ asset('img/bg-image.jpg') }}');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
        } */
        
        /* Common Body Styles (Always Keep This) */
        body {
            min-height: 100vh;
            background: transparent;
            margin: 0;
            padding: 0;
        }

        h1 {
            color: #fff;
        }
    </style>

    <style type="text/css">
        /*
      * Pattern lock css
      * Pattern direction
      * http://ignitersworld.com/lab/patternLock.html
      */
        .patt-wrap {
            z-index: 10;
        }

        .patt-circ.hovered {
            background-color: #cde2f2;
            border: none;
        }

        .patt-circ.hovered .patt-dots {
            display: none;
        }

        .patt-circ.dir {
            background-image: url("http://pos.test/img/pattern-directionicon-arrow.png");
            background-position: center;
            background-repeat: no-repeat;
        }

        .patt-circ.e {
            -webkit-transform: rotate(0);
            transform: rotate(0);
        }

        .patt-circ.s-e {
            -webkit-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .patt-circ.s {
            -webkit-transform: rotate(90deg);
            transform: rotate(90deg);
        }

        .patt-circ.s-w {
            -webkit-transform: rotate(135deg);
            transform: rotate(135deg);
        }

        .patt-circ.w {
            -webkit-transform: rotate(180deg);
            transform: rotate(180deg);
        }

        .patt-circ.n-w {
            -webkit-transform: rotate(225deg);
            transform: rotate(225deg);
        }

        .patt-circ.n {
            -webkit-transform: rotate(270deg);
            transform: rotate(270deg);
        }

        .patt-circ.n-e {
            -webkit-transform: rotate(315deg);
            transform: rotate(315deg);
        }
    </style>
    <style>
        h1 {
            color: #fff;
        }
    </style>
    <style>
        .action-link[data-v-1552a5b6] {
            cursor: pointer;
        }
    </style>
    <style>
        .action-link[data-v-397d14ca] {
            cursor: pointer;
        }
    </style>
    <style>
        .action-link[data-v-49962cc0] {
            cursor: pointer;
        }
    </style>

<link href="{{ asset('css/tailwind/app.css') }}" rel="stylesheet">

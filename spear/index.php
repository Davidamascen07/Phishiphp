<?php
   require_once(dirname(__FILE__) . '/manager/session_manager.php');
   if(isSessionValid() == true){
     header("Location: Home");
     die();
  }
   
  if (!empty($_POST['username']) && !empty($_POST['password'])) {
     if(validateLogin($_POST['username'],$_POST['password']) == true){
       createSession(true,$_POST['username']);
       header("Location: Home");
       die();
     }  
   }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>LooPhish - Entrar</title>
   <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
   <!-- Project core CSS -->

   <!-- <link rel="stylesheet" href="css/style2.css">
   <link rel="stylesheet" href="css/sidebar-modern.css"> -->
   <link rel="stylesheet" href="css/neumorphism-login.css">
   <link rel="stylesheet" href="css/cyberphishcx.css">
   <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
   <style>
      /* Error styles for login inputs when authentication fails */
      .flash-message.error {
         background: #fee2e2;
         color: #991b1b;
         border: 1px solid #fecaca;
         padding: 10px 12px;
         border-radius: 8px;
         margin-bottom: 12px;
         font-weight: 600;
      }

      .login-card.has-error .neu-input input {
         color: #7f1d1d !important;
         border-color: #fca5a5 !important;
         box-shadow: 0 0 0 6px rgba(252, 165, 165, 0.08) !important;
      }

      .login-card.has-error .neu-input label {
         color: #b91c1c !important;
      }

      @keyframes denyPulse {
         0% { transform: translateY(0); }
         30% { transform: translateY(-3px); }
         60% { transform: translateY(0); }
         100% { transform: translateY(0); }
      }

      .login-card.has-error .neu-input {
         animation: denyPulse 420ms ease-in-out;
      }
   </style>
</head>
<body>
   <div class="login-container">
      <div class="login-card">
         <div class="login-form">
            <div class="login-header">
               <div class="neu-icon">
                  <div class="icon-inner">
                     <img src="images/logo-icon.png" alt="logo" class="login-logo" style="width:64px;height:64px;border-radius:50%;object-fit:cover;display:block;margin:0 auto;">
                  </div>
               </div>
               <h2>Bem-vindo</h2>
               <p>Entre para continuar na plataforma LooPhish</p>
            </div>

            <?php if (!empty(
                    
                    
               $_SESSION['flash'])): $flash = $_SESSION['flash']; ?>
               <div class="flash-message <?php echo htmlspecialchars($flash['type']); ?>">
                  <?php echo htmlspecialchars($flash['message']); ?>
               </div>
            <?php unset($_SESSION['flash']); endif; ?>

            <?php 
               if(isset($_POST['username']) || isset($_POST['password']))
                  echo '<div class="flash-message error">Username ou senha incorretos.</div>';
            ?>

            <form id="loginForm" action="index.php" method="post" novalidate>
               <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>">
               <div class="form-group">
                  <div class="input-group neu-input">
                     <input type="text" id="email" name="username" required autocomplete="username" placeholder=" ">
                     <label for="email">Usuário ou Email</label>
                     <div class="input-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                           <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                           <circle cx="12" cy="7" r="4"/>
                        </svg>
                     </div>
                  </div>
                  <span class="error-message" id="emailError"></span>
               </div>

               <div class="form-group">
                  <div class="input-group neu-input password-group">
                     <input type="password" id="password" name="password" required autocomplete="current-password" placeholder=" ">
                     <label for="password">Senha</label>
                     <div class="input-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                           <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                           <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                     </div>
                     <button type="button" class="password-toggle neu-toggle" id="passwordToggle" aria-label="Toggle password visibility">
                        <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                           <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                           <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                           <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                           <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                     </button>
                  </div>
                  <span class="error-message" id="passwordError"></span>
               </div>

               <div class="form-options">
                  <a href="#" class="forgot-link" id="forgotLink">Esqueci minha senha</a>
               </div>

               <button type="submit" class="neu-button login-btn">
                  <span class="btn-text">Entrar</span>
                  <div class="btn-loader">
                     <div class="neu-spinner"></div>
                  </div>
               </button>
            </form>
         </div>
      </div>
   </div>

   <script>
      // simple password toggle
      document.addEventListener('DOMContentLoaded', function(){
         var toggle = document.getElementById('passwordToggle');
         var pwd = document.getElementById('password');
         if(toggle && pwd){
            toggle.addEventListener('click', function(){
               if(pwd.type === 'password') pwd.type = 'text'; else pwd.type = 'password';
            });
         }
      });
   </script>

   <script src="js/libs/jquery/jquery-3.6.0.min.js"></script>
   <script>
      $(function(){
         // recovery toggle placeholder replicating previous behaviour
         $(document).on('click','#to-recover', function(){
            // show recovery UI if implemented
         });
      });
   </script>
</body>
</html>
<?php
require_once(dirname(__FILE__) . '/manager/session_manager.php');
isSessionValid(true);
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <!-- Tell the browser to be responsive to screen width -->
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="description" content="">
   <meta name="author" content="">
   <!-- Favicon icon -->
   <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
   <title>SniperPhish - The Web-Email Spear Phishing Toolkit</title>
   <!-- Custom CSS -->
   <link rel="stylesheet" type="text/css" href="css/select2.min.css">
   <link rel="stylesheet" type="text/css" href="css/jquery.steps.css">
   <link rel="stylesheet" type="text/css" href="css/steps.css">
   <link rel="stylesheet" type="text/css" href="css/prism.css" />
   <link rel="stylesheet" type="text/css" href="css/style.min.css">
   <style>
      .tab-header {
         list-style-type: none;
      }

      pre {
         max-height: 1000px !important;
         /*workaround for Prism scrollbars*/
      }
      
      /* Estilo para validação do Select2 */
      .select2-container.is-invalid .select2-selection--single {
         border-color: #dc3545 !important;
         box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
      }
      
      .select2-container.is-invalid .select2-selection--single:focus {
         border-color: #dc3545 !important;
         box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
      }
   </style>
   <link rel="stylesheet" type="text/css" href="css/toastr.min.css">
</head>

<body>
   <!-- ============================================================== -->
   <!-- Preloader - style you can find in spinners.css -->
   <!-- ============================================================== -->
   <div class="preloader">
      <div class="lds-ripple">
         <div class="lds-pos"></div>
         <div class="lds-pos"></div>
      </div>
   </div>
   <!-- ============================================================== -->
   <!-- Main wrapper - style you can find in pages.scss -->
   <!-- ============================================================== -->
   <div id="main-wrapper">
      <!-- ============================================================== -->
      <!-- Topbar header - style you can find in pages.scss -->
      <!-- ============================================================== -->
      <?php include_once 'z_menu.php' ?>
      <!-- ============================================================== -->
      <!-- End Left Sidebar - style you can find in sidebar.scss  -->
      <!-- ============================================================== -->
      <!-- ============================================================== -->
      <!-- Page wrapper  -->
      <!-- ============================================================== -->
      <div class="page-wrapper">
         <!-- ============================================================== -->
         <!-- Bread crumb and right sidebar toggle -->
         <!-- ============================================================== -->
         <div class="page-breadcrumb">
            <div class="row">
               <div class="col-12 d-flex no-block align-items-center">
                  <h4 class="page-title">Tracker Code Generator</h4>
               </div>
            </div>
         </div>
         <!-- ============================================================== -->
         <!-- End Bread crumb and right sidebar toggle -->
         <!-- ============================================================== -->
         <!-- ============================================================== -->
         <!-- Container fluid  -->
         <!-- ============================================================== -->
         <div class="container-fluid">
            <!-- ============================================================== -->
            <!-- Start Page Content -->
            <!-- ============================================================== -->
            <div class="card">
               <div class="card-body wizard-content">
                  <h5 class="card-title"><strong>Tracker: </strong><span id="tracker_name">New</span></h4>
                     <form id="genreator-form" action="#" class="m-t-20">
                        <div>
                           <h3>Start</h3>
                           <section>
                              <div class="col-md-12">
                                 <div class="row mb-3 align-items-left">
                                    <label for="tb_tracker_name" class="col-sm-2 text-left control-label col-form-label">Tracker Name:</label>
                                    <div class="col-md-7">
                                       <input type="text" class="form-control" id="tb_tracker_name">
                                    </div>
                                 </div>


                                 <div class="row">
                                    <label for="tb_tracker_name" class="col-sm-2 text-left control-label col-form-label">Webhook URL:</label>
                                    <div class="col-md-3">
                                       <select class="select2 form-control custom-select" id="selector_webhook_type" style="height: 36px;width: 100%;">
                                          <option value="sp_base">SP base URL</option>
                                          <option value="current_domain">Current domain</option>
                                          <option value="cust_sp">Custom SP URL</option>
                                       </select>
                                    </div>
                                    <div class="col-md-4">
                                       <input type="text" class="form-control" id="tb_webhook_url">
                                       <div class="text-right m-t-5">
                                          <i class="mdi mdi-information cursor-pointer" data-container="body" tabindex="0" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="SniperPhish URL to which webhook is received from phishing websites. Thish should be accessible for target users."></i>
                                          <button type="button" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="Verify access to the Webhook URL" onclick="webhookValidate($(this))"><i class="fa fas fa-check"></i></button>
                                       </div>
                                    </div>
                                    <div class="col-md-2">
                                       <div class="row">
                                          <div class="col-md-7">
                                             <span class="m-t-5 d-block">/track</span>
                                          </div>
                                       </div>
                                    </div>
                                 </div>

                                 <div class="row">
                                    <label for="cb_auto_ativate" class="col-sm-2 text-left control-label col-form-label m-t-10">Auto-activate after creation</label>
                                    <div class="custom-control custom-switch col-sm-3 m-t-15 row">
                                       <label class="switch">
                                          <input type="checkbox" id="cb_auto_ativate" checked>
                                          <span class="slider round"></span>
                                       </label>
                                    </div>
                                 </div>
                              </div>

                              <!--<p>(*) Mandatory</p> -->
                           </section>
                           <h3>Training Integration</h3>
                           <section>
                              <div class="col-md-12">
                                 <div class="row mb-3">
                                    <div class="col-md-12">
                                       <h6 class="hbar">Training Module Association</h6>
                                       <p>Associate this phishing campaign with training modules to enhance user awareness.</p>
                                    </div>
                                 </div>
                                 
                                 <div class="row mb-3">
                                    <label for="cb_training_enabled" class="col-sm-3 text-left control-label col-form-label">Enable Training Integration</label>
                                    <div class="custom-control custom-switch col-sm-3 m-t-15 row">
                                       <label class="switch">
                                          <input type="checkbox" id="cb_training_enabled">
                                          <span class="slider round"></span>
                                       </label>
                                    </div>
                                    <div class="col-sm-6">
                                       <i class="mdi mdi-information cursor-pointer m-t-5" tabindex="0" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="When enabled, users who interact with this tracker will be redirected to training modules."></i>
                                    </div>
                                 </div>

                                 <div id="training_options_area" style="display: none;">
                                    <div class="row mb-3">
                                       <label for="select_training_module" class="col-sm-3 text-left control-label col-form-label">Training Module</label>
                                       <div class="col-sm-8">
                                          <select class="select2 form-control custom-select" id="select_training_module" style="height: 36px;width: 100%;">
                                             <option value="">Select Training Module...</option>
                                             <!-- Options will be loaded dynamically -->
                                          </select>
                                       </div>
                                       <div class="col-sm-1">
                                          <i class="mdi mdi-information cursor-pointer m-t-5" tabindex="0" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="Select which training module users will be redirected to after interaction."></i>
                                       </div>
                                    </div>

                                    <div class="row mb-3">
                                       <label for="select_training_trigger" class="col-sm-3 text-left control-label col-form-label">Training Trigger</label>
                                       <div class="col-sm-8">
                                          <select class="form-control custom-select" id="select_training_trigger" style="height: 36px;width: 100%;">
                                             <option value="immediate">Immediate - Right after click</option>
                                             <option value="on_completion">On Completion - After completing the phishing flow</option>
                                             <option value="on_failure">On Failure - After failed phishing attempt</option>
                                             <option value="on_interaction">On Interaction - After any form interaction</option>
                                          </select>
                                       </div>
                                       <div class="col-sm-1">
                                          <i class="mdi mdi-information cursor-pointer m-t-5" tabindex="0" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="Define when the training module should be triggered during the phishing simulation."></i>
                                       </div>
                                    </div>

                                    <div class="row mb-3">
                                       <label for="tb_training_redirect_url" class="col-sm-3 text-left control-label col-form-label">Custom Redirect URL</label>
                                       <div class="col-sm-8">
                                          <input type="text" class="form-control" id="tb_training_redirect_url" placeholder="https://example.com/training-complete">
                                       </div>
                                       <div class="col-sm-1">
                                          <i class="mdi mdi-information cursor-pointer m-t-5" tabindex="0" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="Optional: URL to redirect after training completion. Leave empty to use default."></i>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </section>
                           <h3>Web Pages</h3>
                           <section>
                              <div class="col-md-12">
                                 <div id="webpages_area" class="trans">
                                 </div>
                                 <div class="row mb-3 align-items-left m-t-20">
                                    <label for="phising_site_final_page_url" class="col-sm-2 text-left control-label col-form-label">Final destination URL: </label>
                                    <div class="col-sm-8 custom-control">
                                       <input type="text" class="form-control" id="phising_site_final_page_url" placeholder="eg: https://myphishingsite/thankyou or #">
                                    </div>
                                    <i class="mdi mdi-information cursor-pointer m-t-5" tabindex="0" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="The final landing page to redirect website when sumbission button from the last page is clicked."></i>
                                    <div class="col-md-1 text-right">
                                       <button class="btn btn-info btn-sm bt_delete_page_first" data-toggle="tooltip" data-placement="left" title="Add page" hidden=""><i class="fas fa-level-down-alt"></i></button>
                                    </div>
                                 </div>
                              </div>
                           </section>
                           <h3>Output</h3>
                           <section>
                              <div class="card">
                                 <!-- Nav tabs -->
                                 <div class="form-group row">
                                    <div class="col-md-12">
                                       <h6 class="hbar">Tracker Code </h6>
                                    </div>
                                 </div>
                                 <div class="form-group row">
                                    <div class="col-md-12">
                                       <p>Copy below HTML tracker code under &lt;HEAD&gt; section of all the pages of your phishing website which is to be tracked.</p>
                                       <div class="col-md-12 prism_side-top">
                                          <span>
                                             <button type="button" class="btn waves-effect waves-light btn-xs btn-dark mdi mdi-download" data-toggle="tooltip" title="Download" onClick="downloadCode('html_tracker_code','tracker_link.txt','text/plain')" />
                                             <button type="button" class="btn waves-effect waves-light btn-xs btn-dark mdi mdi-content-copy btn_copy" data-toggle="tooltip" title="Copy" onclick="copyCode('html_tracker_code')" />
                                          </span>
                                       </div>
                                       <pre><code class="language-html html_tracker_code"></code></pre>
                                    </div>
                                 </div>
                                 <div class="row" id="tracker_code_area">
                                 </div>
                                 <hr />
                                 <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item tab-header"> <a class="nav-link active" data-toggle="tab" href="#js" role="tab"><span class="hidden-sm-up"></span> <span class="hidden-xs-down">Tracker Code (Preview)</span></a> </li>
                                    <li class="nav-item tab-header"> <a class="nav-link" data-toggle="tab" href="#html" role="tab"><span class="hidden-sm-up"></span> <span class="hidden-xs-down">Webpage Forms (Preview)</span></a> </li>
                                    <li class="nav-item tab-header"> <a class="nav-link" data-toggle="tab" href="#zip" role="tab"><span class="hidden-sm-up"></span> <span class="hidden-xs-down">Zip Download (Preview)</span></a> </li>
                                 </ul>
                                 <!-- Tab panes -->
                                 <div class="tab-content tabcontent-border">
                                    <div class="tab-pane active" id="js" role="tabpanel">
                                       <div class="p-20" id="js_area">
                                       </div>
                                    </div>
                                    <div class="tab-pane" id="html" role="tabpanel">
                                       <div class="p-20" id="html_area">
                                       </div>
                                    </div>
                                    <div class="tab-pane" id="zip" role="tabpanel">
                                       <div class="p-20" id="zip_area">
                                          <div>
                                             <div class="alert alert-primary" role="alert">Download all files as public_html.zip</div>
                                          </div>
                                          <button type="button" class="btn btn-success btn-lg m-r-10 mdi mdi-folder-download" onClick="downloadCodeAsZip()"> Download</button>
                                          <i class="mdi mdi-information cursor-pointer m-t-5" tabindex="0" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="The basic html website pages generated as per your data"></i>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </section>
                        </div>
                     </form>
               </div>
            </div>
            <!-- ============================================================== -->
            <!-- End PAge Content -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Right sidebar -->
            <!-- ============================================================== -->
            <!-- .right-sidebar -->
            <!-- ============================================================== -->
            <!-- End Right sidebar -->
            <!-- ============================================================== -->
         </div>
         <!-- ============================================================== -->
         <!-- End Container fluid  -->
         <!-- ============================================================== -->
         <!-- ============================================================== -->
         <!-- footer -->
         <!-- ============================================================== -->
         <?php include_once 'z_footer.php' ?>
         <!-- ============================================================== -->
         <!-- End footer -->
         <!-- ============================================================== -->
         <!-- Modal -->
         <div class="modal fade" id="modal_import_html_fields" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-large" role="document">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title">Import HTML fields</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                  </div>
                  <div class="modal-body">
                     <div class="col-md-12">
                        <input type="text" class="col-md-12 form-control" id="tb_import_url" placeholder="Phishing web page URL">
                        <label class="col-sm-6 text-right control-label col-form-label">Or</label>
                        <textarea class="col-md-12 form-control" rows="8" id="ta_HTML_content" placeholder="HTML contents of Phishing web page"></textarea>
                        <div class="row m-t-10">
                           <div class="col-md-10">
                              <div class="progress m-t-15">
                                 <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressbar_status" style="width:0%"></div>
                              </div>
                              <div class="valid-feedback" id="lb_progress"></div>
                           </div>
                           <div class="col-md-2 text-right">
                              <button type="button" class="btn btn-info" onclick="startHTMLFieldFetch()">Start</button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- Modal -->
         <div class="modal fade" id="modal_prompts" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title">Are you sure?</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                  </div>
                  <div class="modal-body" id="modal_prompts_body">
                     content...
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-danger" id="modal_prompts_confirm_button">Delete</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- ============================================================== -->
      <!-- End Page wrapper  -->
      <!-- ============================================================== -->
   </div>
   <!-- ============================================================== -->
   <!-- End Wrapper -->
   <!-- ============================================================== -->
   <!-- ============================================================== -->
   <!-- All Jquery -->
   <!-- ============================================================== -->
   <script src="js/libs/jquery/jquery-3.6.0.min.js"></script>
   <script src="js/libs/jquery/jquery-ui.min.js"></script>
   <script src="js/libs/js.cookie.min.js"></script>
   <!-- Bootstrap tether Core JavaScript -->
   <script src="js/libs/perfect-scrollbar.jquery.min.js"></script>
   <script src="js/libs/custom.min.js"></script>
   <!-- this page js -->
   <script src="js/libs/jquery/jquery.steps.min.js"></script>
   <script src="js/libs/clipboard.min.js"></script>
   <script src="js/libs/select2.min.js"></script>
   <script src="js/common_scripts.js"></script>
   <script src="js/web_tracker_generator_function.js"></script>
   <script>
      // Training Integration JavaScript
      $(document).ready(function() {
         // Toggle training options visibility
         $('#cb_training_enabled').change(function() {
            if ($(this).is(':checked')) {
               $('#training_options_area').show();
               loadTrainingModules();
            } else {
               $('#training_options_area').hide();
            }
         });

         // Initialize Select2 for training module
         $('#select_training_module').select2({
            placeholder: 'Select Training Module...',
            allowClear: true
         });
         
         // Remove validation error when module is selected
         $('#select_training_module').on('change', function() {
            if ($(this).val() != "" && $(this).val() != null) {
               $(this).next('.select2-container').removeClass('is-invalid');
            }
         });
      });

      // Load available training modules
      function loadTrainingModules() {
         return new Promise(function(resolve, reject) {
            $.post({
               url: "manager/training_integration_manager",
               contentType: 'application/json; charset=utf-8',
               data: JSON.stringify({ 
                  action_type: "get_training_modules"
               }),
            }).done(function (response) {
               if(response.result == "success") {
                  var select = $('#select_training_module');
                  select.empty();
                  select.append('<option value="">Select Training Module...</option>');
                  
                  $.each(response.modules, function(index, module) {
                     select.append('<option value="' + module.module_id + '">' + module.module_name + '</option>');
                  });
                  
                  select.trigger('change');
                  resolve();
               } else {
                  reject(response.error);
               }
            }).fail(function() {
               // Fallback - add some default options if training module loading fails
               var select = $('#select_training_module');
               select.empty();
               select.append('<option value="">Select Training Module...</option>');
               select.append('<option value="phishing_awareness">Phishing Awareness Training</option>');
               select.append('<option value="security_basics">Security Basics</option>');
               select.append('<option value="email_safety">Email Safety</option>');
               resolve();
            });
         });
      }

      var form = $("#genreator-form");

      form.children("div").steps({
         headerTag: "h3",
         bodyTag: "section",
         transitionEffect: "slide",
         onStepChanging: function(event, currentIndex, newIndex) {
            console.log('Step changing from', currentIndex, 'to', newIndex); // Debug
            
            $('[data-toggle="popover"]').popover('hide');
            if (currentIndex > newIndex)
               return true;

            var f_error = false;
            
            // Step 0: Start (Tracker Name, Webhook URL)
            if (currentIndex == 0) {
               console.log('Validating Step 0 - Start'); // Debug
               
               if ($("#tb_tracker_name").val() == "") {
                  $("#tb_tracker_name").addClass("is-invalid");
                  f_error = true;
               } else
                  $("#tb_tracker_name").removeClass("is-invalid");

               if (isValidURL($("#tb_webhook_url").val()) == false) {
                  $("#tb_webhook_url").addClass("is-invalid");
                  f_error = true;
               } else
                  $("#tb_webhook_url").removeClass("is-invalid");
            }
            
            // Step 1: Training Integration (optional validations)
            if (currentIndex == 1) {
               console.log('Validating Step 1 - Training Integration'); // Debug
               console.log('Training enabled:', $("#cb_training_enabled").is(':checked')); // Debug
               
               // Se treinamento está habilitado, validar campos obrigatórios
               if ($("#cb_training_enabled").is(':checked')) {
                  if ($("#select_training_module").val() == "" || $("#select_training_module").val() == null) {
                     $("#select_training_module").next('.select2-container').addClass('is-invalid');
                     console.log('Training module validation failed'); // Debug
                     f_error = true;
                  } else {
                     $("#select_training_module").next('.select2-container').removeClass('is-invalid');
                  }
               }
               // Se treinamento não está habilitado, sempre permitir continuar
            }

            // Step 2: Web Pages (antigo Step 1)
            if (currentIndex == 2) {
               console.log('Validating Step 2 - Web Pages'); // Debug
               
               $('input[name="field_page_name"]').each(function() {
                  $(this).removeClass("is-invalid");
               });
               $('input[name="field_page_url"]').each(function() {
                  $(this).removeClass("is-invalid");
               });
               $('select[name="field_type_names"]').each(function() { // remove all red lines initially
                  $(this).data('select2').$selection.addClass("select2-selection");
                  $(this).data('select2').$selection.removeClass("select2-is-invalid");
               });
               $('input[name="field_id_names"]').each(function() {
                  $(this).removeClass("is-invalid");
               });
               $("#phising_site_final_page_url").removeClass("is-invalid");
               //------------------------

               $('.new_webpage').each(function(i, obj) {
                  var arr_filed_types = $.map($(obj).find('select[name="field_type_names"]'), function(e) {
                     return $('option:selected', e).val();
                  });
                  var FSB_count = arr_filed_types.reduce(function(n, val) {
                     return n + (val === 'FSB');
                  }, 0);

                  if (FSB_count == 0) { //if no submission button
                     $(obj).find(".bt_add_field_set").trigger("click");
                     $(obj).find('select[name="field_type_names"]:last').val('FSB').trigger("change");
                  } else
                  if (FSB_count > 1) { //if more than 1 submission button
                     f_error = true;
                     var arr_fsb_elements = $.map($(obj).find('select[name="field_type_names"]'), function(e) {
                        if ($(e).val() == "FSB") return e;
                     });
                     arr_fsb_elements.shift();
                     $.each(arr_fsb_elements, function() {
                        $(this).data('select2').$selection.removeClass("select2-selection");
                        $(this).data('select2').$selection.addClass("select2-is-invalid");
                     });
                  }
               });

               $('input[name="field_page_name"]').each(function() {
                  if ($(this).val().trim().length == 0) {
                     $(this).addClass("is-invalid");
                     f_error = true;
                  }
               });
               $('input[name="field_page_url"]').each(function() {
                  if ($(this).val().trim() != "#" && !isValidURL($(this).val())) {
                     $(this).addClass("is-invalid");
                     f_error = true;
                  }
               });
               $('input[name="field_id_names"]').each(function() {
                  if ($(this).val() == '') {
                     $(this).addClass("is-invalid");
                     f_error = true;
                  }
               });

               if ($("#phising_site_final_page_url").val().trim() != "#" && !isValidURL($("#phising_site_final_page_url").val())) {
                  $("#phising_site_final_page_url").addClass("is-invalid");
                  f_error = true;
               }
            }

            console.log('Validation errors:', f_error); // Debug
            
            if (f_error)
               return false;

            // Step 2 (Web Pages) - Generate code
            if (currentIndex == 2) {
               generateFormFields();
               generateTrackerCode();
               saveWebTracker(''); // auto-save in final page
            }

            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
         },
         onFinished: function(event, currentIndex) {
            $('#genreator-form').find('a[href="#finish"]').html('<i class="fa fas"></i> Save'); //For button loader
            <?php
            if (isset($_GET['tracker']))
               echo 'saveWebTracker("' . doFilter($_GET['tracker'], 'ALPHA_NUM') . '");';
            else
               echo "saveWebTracker('');";
            ?>

         }
      });
   </script>
   <script>
      <?php
      if (isset($_GET['tracker']))
         echo 'editWebTracker("' . doFilter($_GET['tracker'], 'ALPHA_NUM') . '");';
      ?>
   </script>
   <script defer src="js/libs/sidebarmenu.js"></script>
   <script defer src="js/libs/popper.min.js"></script>
   <script defer src="js/libs/bootstrap.min.js"></script>
   <script defer src="js/libs/jquery/jquery.validate.min.js"></script>
   <script defer src="js/libs/toastr.min.js"></script>
   <script defer src="js/libs/moment.min.js"></script>
   <script defer src="js/libs/moment-timezone-with-data.min.js"></script>
   <script defer src="js/libs/beautify.min.js"></script>
   <script defer src="js/libs/beautify-html.min.js"></script>
   <script defer src="js/libs/prism.js"></script>
   <script defer src="js/libs/jszip.min.js"></script>

</body>

</html>
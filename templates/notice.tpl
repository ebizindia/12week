<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
/* ============================================
   MOBILE-FIRST NOTICE/EMAIL DESIGN
   ============================================ */

:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --danger-gradient: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
    --warning-gradient: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
    --info-gradient: linear-gradient(135deg, #2196F3 0%, #00BCD4 100%);
    --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    
    --text-primary: #2c3e50;
    --text-secondary: #7f8c8d;
    
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
    
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
    --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
    
    --spacing-xs: 8px;
    --spacing-sm: 12px;
    --spacing-md: 16px;
    --spacing-lg: 24px;
    --spacing-xl: 32px;
}

* {
    -webkit-tap-highlight-color: transparent;
}

body {
    background: var(--bg-gradient);
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    -webkit-font-smoothing: antialiased;
}

.notice-wrapper {
    min-height: 100vh;
    padding: 4px;
    padding-bottom: 80px;
}

.notice-card {
    background: white;
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

.notice-header {
    background: var(--primary-gradient);
    color: white;
    padding: var(--spacing-md) var(--spacing-sm);
}

.notice-header h4 {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.notice-body {
    padding: var(--spacing-md) var(--spacing-sm);
}

.alert-modern {
    border-radius: var(--radius-md);
    border: none;
    padding: var(--spacing-md);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-sm);
}

.alert-modern.alert-warning {
    background: #fff3cd;
    color: #856404;
}

.alert-modern.alert-danger {
    background: #f8d7da;
    color: #721c24;
}

.alert-modern.alert-success {
    background: #d4edda;
    color: #155724;
}

.required {
    color: #dc3545;
    font-weight: 700;
}

.form-group {
    margin-bottom: var(--spacing-lg);
}

.form-group label {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--spacing-xs);
    display: block;
    font-size: 0.938rem;
}

.mandatory {
    color: #dc3545;
    margin-left: 2px;
}

.form-control {
    border-radius: var(--radius-md);
    border: 2px solid #e9ecef;
    padding: var(--spacing-sm);
    font-size: 1rem;
    width: 100%;
    min-height: 44px;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
    outline: none;
}

textarea.form-control {
    min-height: 150px;
    resize: vertical;
}

.dnd_chkbox {
    width: 20px;
    height: 20px;
    min-height: auto;
    cursor: pointer;
    accent-color: #667eea;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    cursor: pointer;
    font-weight: 500;
}

.form-elem-guide-text {
    margin-top: var(--spacing-xs);
    padding: var(--spacing-sm);
    background: #f8f9fa;
    border-radius: var(--radius-md);
    font-size: 0.813rem;
    color: var(--text-secondary);
}

/* Recipients Section */
.recipients-header {
    display: flex;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-sm);
}

.recipients-header a {
    color: #667eea;
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
}

.recipients-header a:hover {
    text-decoration: underline;
}

.notice_groups_cont {
    max-height: 200px;
    border: 2px solid #e9ecef;
    border-radius: var(--radius-md);
    padding: var(--spacing-sm);
    overflow-y: auto;
    background: #f8f9fa;
}

.checkbox_group {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: var(--spacing-xs);
    margin-bottom: var(--spacing-xs);
    background: white;
    border-radius: var(--radius-md);
    transition: all 0.3s ease;
}

.checkbox_group:hover {
    background: #f1f3f5;
}

.checkbox_group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #667eea;
    flex-shrink: 0;
}

.checkbox_group label {
    margin: 0;
    cursor: pointer;
    font-weight: 500;
    font-size: 0.875rem;
    flex: 1;
}

/* WhatsApp Section */
.whatsapp-section {
    background: #f8f9fa;
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
}

.whatsapp-header {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-md);
}

.whatsapp-header h5 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.whatsapp-toggle {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: var(--spacing-xs) var(--spacing-sm);
    background: white;
    border-radius: var(--radius-xl);
}

/* File Upload */
.file-upload-wrapper {
    position: relative;
}

.file-clear-btn {
    color: #dc3545;
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    display: inline-block;
    margin-top: var(--spacing-xs);
}

.file-clear-btn:hover {
    text-decoration: underline;
}

/* CKEditor */
.ck-editor__editable_inline:not(.ck-comment__input *) {
    min-height: 250px;
    border-radius: var(--radius-md);
}

.ck-editor__editable_inline.ck-read-only {
    background-color: #f1f1f1 !important;
}

/* Submit Button */
.form-actions {
    margin-top: var(--spacing-xl);
    text-align: center;
}

.btn-submit {
    background: var(--success-gradient);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    padding: var(--spacing-md) var(--spacing-xl);
    font-weight: 600;
    font-size: 1rem;
    width: 100%;
    min-height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
    transition: all 0.3s ease;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-submit img {
    width: 20px;
    height: auto;
    filter: brightness(0) invert(1);
}

/* Divider */
.divider {
    border: none;
    border-top: 2px solid #e9ecef;
    margin: var(--spacing-lg) 0;
}

/* Scrollbar Styling */
.notice_groups_cont::-webkit-scrollbar {
    width: 6px;
}

.notice_groups_cont::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: var(--radius-xl);
}

.notice_groups_cont::-webkit-scrollbar-thumb {
    background: #667eea;
    border-radius: var(--radius-xl);
}

/* Tablet+ Responsive */
@media (min-width: 768px) {
    .notice-wrapper {
        padding: var(--spacing-md);
    }
    
    .notice-header {
        padding: var(--spacing-lg);
    }
    
    .notice-header h4 {
        font-size: 1.5rem;
    }
    
    .notice-body {
        padding: var(--spacing-xl);
    }
    
    .form-group-row {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: var(--spacing-lg);
        align-items: start;
    }
    
    .form-group-row label {
        padding-top: var(--spacing-sm);
        text-align: right;
    }
    
    .btn-submit {
        width: auto;
        min-width: 250px;
    }
    
    textarea.form-control {
        min-height: 200px;
    }
    
    .ck-editor__editable_inline:not(.ck-comment__input *) {
        min-height: 350px;
    }
}

@media (min-width: 992px) {
    .notice-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: var(--spacing-lg);
    }
}

/* Animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notice-card {
    animation: slideIn 0.3s ease-out;
}
</style>

<div class="notice-wrapper">
    <div class="notice-card">
        <div class="notice-header">
            <h4>
                <i class="fas fa-envelope"></i>
                <span>Send Email To Members</span>
            </h4>
        </div>
        
        <div class="notice-body">
            <form class="form-horizontal" role="form" name='feedbackform' id="feedbackform" 
                  action='notice.php' method='post' 
                  onsubmit="return noticefuncs.submitNotice(this);" 
                  target="form_post_submit_target_window" 
                  data-mode="sendnotice" novalidate enctype="multipart/form-data">
                
                <input type='hidden' name='mode' id='send_feedback' value='sendnotice' />
                
                <!-- Info Alert -->
                <div class="alert alert-warning alert-modern" role="alert">
                    <p style="margin-bottom: 0">
                        All fields marked with an asterisk (<span class="required">*</span>) are required.
                    </p>
                </div>

                <!-- Error Alert (hidden by default) -->
                <div class="alert alert-danger alert-modern d-none">
                    <strong><i class="fas fa-times-circle"></i></strong>
                    <span class="alert-message"></span>
                </div>

                <!-- Success Alert (hidden by default) -->
                <div class="alert alert-success alert-modern d-none">
                    <strong><i class="fas fa-check-circle"></i></strong>
                    <span class="alert-message"></span>
                </div>

                <!-- Test Email Checkbox -->
                <div class="form-group">
                    <label for="add_form_field_msgtestnotice">
                        <i class="fas fa-vial"></i> Send Test Email
                    </label>
                    <div class="checkbox-label">
                        <input id="add_form_field_msgtestnotice" 
                               class="dnd_chkbox" 
                               type="checkbox" 
                               name='msg_test_notice' 
                               value="1" 
                               autocomplete="off" 
                               checked 
                               data-email="<?php echo $this->base_template_data['loggedindata'][0]['profile_details']['email']; ?>" 
                               data-wa="<?php echo $this->base_template_data['loggedindata'][0]['profile_details']['mobile']; ?>">
                        <label for="add_form_field_msgtestnotice">
                            Test email will be sent to your email and mobile
                        </label>
                    </div>
                </div>

                <hr class="divider">

                <!-- Recipients -->
                <div class="form-group">
                    <label for="add_form_field_groups">
                        <i class="fas fa-users"></i> To <span class="mandatory">*</span>
                    </label>
                    <div class="recipients-header">
                        <a href="#" id="selallgrps" class="togglegrpsel">
                            <i class="fas fa-check-double"></i> Select All
                        </a>
                        <a href="#" id="deselallgrps" class="togglegrpsel">
                            <i class="fas fa-times"></i> Deselect All
                        </a>
                    </div>
                    <div class="notice_groups_cont">
                        <?php foreach ($this->body_template_data['members'] as $mem) { ?>
                        <div class="checkbox_group">
                            <input type='checkbox' 
                                   value="<?php echo $mem['id']; ?>" 
                                   name="members[]" 
                                   id="add_form_field_member_<?php echo $mem['id']; ?>" 
                                   checked="checked">
                            <label for="add_form_field_member_<?php echo $mem['id']; ?>">
                                <?php echo \eBizIndia\_esc($mem['name']); ?>
                            </label>
                        </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Subject -->
                <div class="form-group">
                    <label for="add_form_field_msgsub">
                        <i class="fas fa-heading"></i> Subject <span class="mandatory">*</span>
                    </label>
                    <input type="text" 
                           id="add_form_field_msgsub" 
                           placeholder="Enter email subject" 
                           class="form-control" 
                           name='msg_sub' 
                           autocomplete="off" 
                           maxlength="250">
                </div>

                <!-- Message -->
                <div class="form-group">
                    <label for="add_form_field_msgbody">
                        <i class="fas fa-comment-alt"></i> Message <span class="mandatory">*</span>
                    </label>
                    <textarea id="add_form_field_msgbody" 
                              placeholder="Enter your message here" 
                              class="form-control" 
                              name='msg_body' 
                              autocomplete="off"></textarea>
                    <div class="form-elem-guide-text">
                        <p style="margin-bottom: var(--spacing-xs);">
                            <strong>Available Placeholders:</strong>
                        </p>
                        <p style="margin: 0;">
                            <?php echo implode(', ', array_keys(CONST_NOTICE_EMAIL_VARS)); ?>
                        </p>
                        <p style="margin-top: var(--spacing-xs); margin-bottom: 0;">
                            These placeholders will be automatically replaced with member-specific data.
                        </p>
                    </div>
                </div>

                <!-- Attachment -->
                <div class="form-group">
                    <label for="add_form_field_attachment">
                        <i class="fas fa-paperclip"></i> Attachment
                    </label>
                    <div class="file-upload-wrapper">
                        <input type="file" 
                               id="add_form_field_attachment" 
                               class="form-control" 
                               name='attachment' 
                               autocomplete="off" 
                               accept="<?php echo '.'.implode(', .', $this->body_template_data['attachment_types']); ?>">
                        <a href="#" id="remove_attachment" class="file-clear-btn">
                            <i class="fas fa-times"></i> Clear Selection
                        </a>
                    </div>
                    <div class="form-elem-guide-text">
                        <strong>Allowed file types:</strong> <?php echo implode(', ', $this->body_template_data['attachment_types']); ?>
                    </div>
                </div>

                <?php if(ENABLE_WHATSAPP_MSG == 1){ ?>
                <hr class="divider">

                <!-- WhatsApp Section -->
                <div class="whatsapp-section">
                    <div class="whatsapp-header">
                        <h5>
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </h5>
                        <div class="whatsapp-toggle">
                            <input id="add_form_field_sendwamsg" 
                                   class="dnd_chkbox" 
                                   type="checkbox" 
                                   name="send_via_wa" 
                                   value="1" 
                                   autocomplete="off" 
                                   checked="checked">
                            <label for="add_form_field_sendwamsg" style="cursor: pointer; margin: 0;">
                                Send over WhatsApp too
                            </label>
                        </div>
                    </div>

                    <!-- Campaign -->
                    <div class="form-group">
                        <label for="add_form_field_msgcampaign">
                            <i class="fas fa-bullhorn"></i> Campaign <span class="mandatory">*</span>
                        </label>
                        <input type="text" 
                               id="add_form_field_msgcampaign" 
                               placeholder="WhatsApp campaign name" 
                               class="form-control" 
                               name='msg_campaign' 
                               autocomplete="off" 
                               maxlength="250">
                    </div>

                    <!-- Replace Vars -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="add_form_field_msgreplacements">
                            <i class="fas fa-exchange-alt"></i> Replace vars with
                        </label>
                        <textarea id="add_form_field_msgreplacements" 
                                  placeholder="Enter replacements here" 
                                  class="form-control" 
                                  name='msg_replacements' 
                                  autocomplete="off" 
                                  maxlength="<?php echo $this->body_template_data['feedback_max_chars']; ?>"></textarea>
                        <div class="form-elem-guide-text">
                            <strong>Valid vars:</strong> $fname, $lname, $email, $password, $membership_no, $batch_no, or fixed text
                        </div>
                    </div>
                </div>
                <?php } ?>

                <!-- Submit Button -->
                <div class="form-actions">
                    <button class="btn-submit" type="submit" id="record-save-button">
                        <img src="images/check.png" alt="Check">
                        <span>Send Email</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
/*$(document).ready(function() {
    // Select/Deselect all recipients
    $('#selallgrps').click(function(e) {
        e.preventDefault();
        $('.notice_groups_cont input[type="checkbox"]').prop('checked', true);
    });
    
    $('#deselallgrps').click(function(e) {
        e.preventDefault();
        $('.notice_groups_cont input[type="checkbox"]').prop('checked', false);
    });
    
    // Clear file selection
    $('#remove_attachment').click(function(e) {
        e.preventDefault();
        $('#add_form_field_attachment').val('');
    });
});*/
</script>
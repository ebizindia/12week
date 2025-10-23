<style>
    #add_form_field_msgbody{
        min-height: 270px;
    }
	.time-input{
		width:15%;
	}
	.time-input input[type="text"]{
		width: 72px;
		float: left;
	}
	.time-input select{
		width: 60px;
	  float: left;
	  margin-left: 10px;
	}	
	.start-end-time{
		width: 30%;
		white-space:nowrap;
	}
	
	.start-end-time label{
		float: left;
	  white-space: nowrap;
	  margin-right: 10px;
	  margin-top: 4px;
	}
	.start-end-time input[type="text"]{
		width: 72px;
		float: left;
	}
	.delete-row{
		font-size: 25px;
		font-weight: bold;
		height: 30px;
		line-height: 0px;
		padding: 2px 11px 6px;
	}
    .ck-editor__editable_inline {
    min-height: 250px;
    }
	@media (max-width: 767px) {
	.btn.back-to-list-button {
		display: none;
		margin-right: -15px;
	  }
	}
	
	/* Select2 Bootstrap 4 styling */
	.select2-container--bootstrap4 .select2-selection--multiple {
		min-height: 38px;
		border: 1px solid #ced4da;
		border-radius: 0.25rem;
	}
	
	.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
		background-color: #007bff;
		border-color: #007bff;
		color: #fff;
		padding: 2px 8px;
		margin: 2px;
		border-radius: 3px;
	}
	
	.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
		color: #fff;
		margin-right: 5px;
	}
	
	.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover {
		color: #fff;
	}
	
	/* Custom multi-select styling */
	.custom-multiselect {
		min-height: 120px;
		max-height: 200px;
		overflow-y: auto;
		border: 1px solid #ced4da;
		border-radius: 0.25rem;
		padding: 5px;
	}
	
	.custom-multiselect .form-check {
		margin-bottom: 5px;
	}
	
	.custom-multiselect .form-check-input {
		margin-right: 8px;
	}
	
	.selected-members {
		margin-top: 10px;
	}
	
	.selected-member-tag {
		display: inline-block;
		background-color: #007bff;
		color: white;
		padding: 4px 8px;
		margin: 2px;
		border-radius: 3px;
		font-size: 12px;
	}
	
	.selected-member-tag .remove-tag {
		margin-left: 5px;
		cursor: pointer;
		font-weight: bold;
	}
	
	.search-members {
		width: 100%;
		margin-bottom: 10px;
		padding: 8px;
		border: 1px solid #ced4da;
		border-radius: 0.25rem;
	}
</style>
<div class="row">
    <div id='feedback_form_container' class="col-12 mt-1 mb-2">
        <div class="card">
            <div class="card-body">                 
                <div class="card-header-heading">
                    <div class="row">
                        <div class="col-8"><h4 class="row pg_heading_line_ht" id="panel-heading-text">Add New Meeting</h4></div>
                        <div class="col-4">
                            <div class="row">                                
                                <div style="text-align:right;width: 100%;">
									<a href="meetings.php" class="btn btn-danger record-list-show-button back-to-list-button rounded" id="back-to-list-button">
									<!--<i class="fa fa-arrow-left"></i>--><img src="images/left-arrow.png" class="custom-button" alt="Left"> Back To List </a>
									
									
									<a href="meetings.php#mode=addUser" class="btn btn-danger record-add-button rounded mobile-bck-to-list" id="back-to-list-button"><!--<i class="fa fa-plus"></i>--><img src="images/left-arrow.png" class="custom-button" alt="Left"></a>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!--meeting-agenda-input start-->
                    
                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 meeting-agenda-input">
                        
                        <form class="form-horizontal" role="form" name='addmeetform' id="addmeetform" action='meetings.php' method='post' onsubmit="return meetfuncs.saveRecDetails(this);" target="form_post_submit_target_window"  data-mode="add-rec" enctype="multipart/form-data" novalidate  >
                        <input type='hidden' name='mode' id='add_edit_mode' value='createrec' />
                        <input type='hidden' name='recordid' id='add_edit_recordid' value='' />
                        <div class="alert alert-warning mt-2 d-none" role="alert" id="msgFrm1">
                            <p style="margin-bottom: 0">Field marked with an asterisk (<span class="required">*</span>) is required.</p>
                        </div>

                        <div class="alert alert-danger d-none">
                            <strong><i class="icon-remove"></i></strong>
                            <span class="alert-message"></span>
                        </div>
                        <div class="alert alert-success d-none">
                            <strong><i class="icon-ok"></i></strong>
                            <span class="alert-message"></span>
                        </div>
                        
                        <div class="form-group">
                          <div class="table-responsive">
                           <table class="table table-striped table-bordered table-hover" style="margin-bottom:0rem;">
                              <thead>
                                <tr>
                                  <th style="width: 15%;">From Date</th>
                                  <th style="width: 15%;">To Date</th>
                                  <th style="width: 10%;">Time</th>
                                  <th>Title</th>
                                  <th>Venue</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>
                                    <input type="date" name="meet_date" id="meet_date" placeholder="Enter Date" class="form-control" style="height:35px;" value="<?php echo date('Y-m-d'); ?>">
                                  </td>
                                  <td>
                                    <input type="date" name="meet_date_to" id="meet_date_to" placeholder="Enter Date" class="form-control" style="height:35px;" value="<?php echo date('Y-m-d'); ?>">
                                  </td>
                                  <td>
                                    <input type="text" name="meet_time" id="meet_time" placeholder="Enter Time" class="form-control">
                                  </td>
                                  <td>
                                    <input type="text" name="meet_title" id="meet_title" placeholder="Enter Title" class="form-control">
                                  </td>
                                  <td>
                                    <input type="text" name="venue" id="venue" placeholder="Enter Venue" class="form-control">
                                  </td>
                                </tr>
                               </tbody>
                            </table>    

                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 400px;">Session</th>
                                        <th colspan="2">Topic</th>
                                    </tr>
                                </thead>
                                <tbody id="session-rows">
                                    <tr>
                                      <td valign="top" style="vertical-align: top;">
                                        <div class="form-row">
                                          <div class="col">
                                            <input type="date" name="session_meet_date[]" id="session_meet_date_0" placeholder="Enter Date" class="form-control" value="<?php echo date('Y-m-d'); ?>" >
                                          </div>
                                          <div class="col-auto d-flex align-items-center">From</div>
                                          <div class="col">
                                            <input type="text" name="time_from[]" id="time_from_0" placeholder="Start Time" class="form-control">
                                          </div>
                                          <div class="col-auto d-flex align-items-center">to</div>
                                          <div class="col">
                                            <input type="text" name="time_to[]" id="time_to_0" placeholder="End Time" class="form-control">
                                          </div>
                                        </div>
                                        <input type="hidden" name="agenda_id[]" value="">
                                      </td>
                                      <td>
                                        <!-- input type="text" name="topic[]" placeholder="Enter Topic" class="form-control" -->
                                        <textarea name="topic[]" id="topic_0"  rows="3" placeholder="Enter Topic" class="form-control"></textarea>
                                      </td>
                                      <td style="width: 60px;"></td> <!-- Reduced width for empty cell -->
                                    </tr>
                                  </tbody>
                                </table>
                            <button type="button" class="btn btn-primary rounded" onclick="meetfuncs.addRow()">Add Session</button>
                          </div>
                        </div>
                        <div class="clearfix"></div>
                        
                        <!-- Absentees Section -->
                        <div id="absenteesBlock">
                            <div class="card-header-heading">
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="row pg_heading_line_ht">Absentees</h4>  
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mt-2 mb-3">
                                    <div class="form-group">
                                        <label for="absentees">Select members who were absent from this meeting:</label>
                                        
                                        <!-- Search input -->
                                        <input type="text" id="member-search" class="search-members d-none" placeholder="Search members..." onkeyup="filterMembers()">
                                        
                                        <!-- Custom multi-select container -->
                                        <div class="custom-multiselect" id="members-list">
                                            <?php if (!empty($this->body_template_data['active_members'])): ?>
                                                <?php foreach ($this->body_template_data['active_members'] as $member): ?>
                                                    <div class="form-check member-item" data-name="<?php echo strtolower(htmlspecialchars($member['name'])); ?>">
                                                        <input class="form-check-input" type="checkbox" name="absentees[]" value="<?php echo $member['id']; ?>" id="member_<?php echo $member['id']; ?>" onchange="updateSelectedMembers()">
                                                        <label class="form-check-label" for="member_<?php echo $member['id']; ?>">
                                                            <?php echo htmlspecialchars($member['name']); ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Selected members display -->
                                        <div class="selected-members" id="selected-members">
                                            <!-- Selected member tags will appear here -->
                                        </div>
                                        
                                        <small class="form-text text-muted">
                                            Search and select multiple members. Leave empty if no one was absent.
                                        </small>
                                        
                                        <!-- Hidden field to store absentees data for population -->
                                        <input type="hidden" id="stored-absentees-data" value="<?php echo htmlspecialchars(json_encode($this->body_template_data['absentees_details'] ?? [])); ?>">
                                        
                                        <!-- Auto-populate trigger -->
                                        <script>
                                        // Auto-populate absentees when page is fully loaded
                                        window.addEventListener('load', function() {
                                            setTimeout(function() {
                                                var storedData = document.getElementById('stored-absentees-data').value;
                                                if (storedData) {
                                                    try {
                                                        var absenteesData = JSON.parse(storedData);
                                                        if (absenteesData.length > 0) {
                                                            // Clear all checkboxes first
                                                            var checkboxes = document.querySelectorAll('input[name="absentees[]"]');
                                                            checkboxes.forEach(function(checkbox) {
                                                                checkbox.checked = false;
                                                            });
                                                            
                                                            // Check the absentees
                                                            absenteesData.forEach(function(absentee) {
                                                                var checkbox = document.getElementById('member_' + absentee.user_id);
                                                                if (checkbox) {
                                                                    checkbox.checked = true;
                                                                }
                                                            });
                                                            
                                                            // Update the selected members display
                                                            if (typeof updateSelectedMembers === 'function') {
                                                                updateSelectedMembers();
                                                            }
                                                        }
                                                    } catch (e) {
                                                        console.log('Error parsing absentees data:', e);
                                                    }
                                                }
                                            }, 500);
                                        });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Presenters Section -->
                        <div id="presentersBlock">
                            <div class="card-header-heading">
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="row pg_heading_line_ht">Presenters</h4>  
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mt-2 mb-3">
                                    <div class="form-group">
                                        <label for="presenters">Select members who were presenters in this meeting:</label>
                                        
                                        <!-- Search input -->
                                        <input type="text" id="presenter-search" class="search-members d-none" placeholder="Search members..." onkeyup="filterPresenters()">
                                        
                                        <!-- Custom multi-select container -->
                                        <div class="custom-multiselect" id="presenters-list">
                                            <?php if (!empty($this->body_template_data['active_members'])): ?>
                                                <?php foreach ($this->body_template_data['active_members'] as $member): ?>
                                                    <div class="form-check presenter-item" data-name="<?php echo strtolower(htmlspecialchars($member['name'])); ?>">
                                                        <input class="form-check-input" type="checkbox" name="presenters[]" value="<?php echo $member['id']; ?>" id="presenter_<?php echo $member['id']; ?>" onchange="updateSelectedPresenters()">
                                                        <label class="form-check-label" for="presenter_<?php echo $member['id']; ?>">
                                                            <?php echo htmlspecialchars($member['name']); ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Selected presenters display -->
                                        <div class="selected-members" id="selected-presenters">
                                            <!-- Selected presenter tags will appear here -->
                                        </div>
                                        
                                        <small class="form-text text-muted">
                                            Search and select multiple members who presented in this meeting. Leave empty if no specific presenters.
                                        </small>
                                        
                                        <!-- Hidden field to store presenters data for population -->
                                        <input type="hidden" id="stored-presenters-data" value="<?php echo htmlspecialchars(json_encode($this->body_template_data['presenters_details'] ?? [])); ?>">
                                        
                                        <!-- Auto-populate trigger -->
                                        <script>
                                        // Auto-populate presenters when page is fully loaded
                                        window.addEventListener('load', function() {
                                            setTimeout(function() {
                                                var storedData = document.getElementById('stored-presenters-data').value;
                                                if (storedData) {
                                                    try {
                                                        var presentersData = JSON.parse(storedData);
                                                        if (presentersData.length > 0) {
                                                            // Clear all checkboxes first
                                                            var checkboxes = document.querySelectorAll('input[name="presenters[]"]');
                                                            checkboxes.forEach(function(checkbox) {
                                                                checkbox.checked = false;
                                                            });
                                                            
                                                            // Check the presenters
                                                            presentersData.forEach(function(presenter) {
                                                                var checkbox = document.getElementById('presenter_' + presenter.user_id);
                                                                if (checkbox) {
                                                                    checkbox.checked = true;
                                                                }
                                                            });
                                                            
                                                            // Update the selected presenters display
                                                            if (typeof updateSelectedPresenters === 'function') {
                                                                updateSelectedPresenters();
                                                            }
                                                        }
                                                    } catch (e) {
                                                        console.log('Error parsing presenters data:', e);
                                                    }
                                                }
                                            }, 500);
                                        });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="minutesBlock">
                        <div class="card-header-heading ">
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="row pg_heading_line_ht">Minutes of Meeting</h4>  
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-center mt-2 mb-3">
                                <textarea name="minutes" id="minutes" class="form-control" rows="10"></textarea>
                               
                            </div>
                        </div>
                    </div>
                       
                        <div class="form-actions form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                <center>
                                    <button class="btn btn-success rounded" type="submit"  id="record-save-button" style="margin-right: 10px;">
                                        <img src="images/check.png" class="check-button" alt="Check"> <span>Add Meeting</span>
                                    </button>
                                    <input type="hidden" id="rec_id" name="rec_id" value="<?php echo (int)$_GET['id']; ?>">
                                    <!-- button type="button" class="btn btn-info rounded" id="download-agenda-btn" onclick="meetfuncs.downloadAgenda(document.getElementById('add_edit_recordid').value)" style="display: none; margin-left: 10px;">
                                        <img src="images/download.png" class="custom-button" alt="Download"> Download Agenda
                                    </button -->
                                    <a href="" class="btn btn-info rounded" id="download-agenda-btn" onclick="return meetfuncs.downloadAgenda(document.getElementById('add_edit_recordid').value)" style="display: none; margin-left: 10px;" download>
                                        <img src="images/download.png" class="custom-button" alt="Download"> Download Agenda
                                    </a>
                                    
                                </center>
                            </div>
                            <div class="col-md-4 col-sm-2 hidden-xs"></div>
                        </div>                 
                        </form>
                    </div>          
                    <!--meeting-agenda-input end-->
                    
                    <!--meeting-agenda-list-view end-->
                    <div class="clearfix"></div>                        
                </div>
				
				
				
				<div class="clearfix"></div>
				<div class="form-actions form-group d-none" style="display: none;">
					<div class="col-md-12 col-sm-12 col-xs-12 text-center">
						<center>
							<button class="btn btn-success rounded" type="submit"  id="record-save-button" style="margin-right: 10px;">
								<img src="images/check.png" class="check-button" alt="Check"> <span>Save</span>
							</button>
						</center>
					</div>
				</div>  
            </div>
        </div>
    </div>
</div>
 <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
 
 <script>
 // Custom multi-select functionality
 function filterMembers() {
     const searchTerm = document.getElementById('member-search').value.toLowerCase();
     const memberItems = document.querySelectorAll('.member-item');
     
     memberItems.forEach(function(item) {
         const memberName = item.getAttribute('data-name');
         if (memberName.includes(searchTerm)) {
             item.style.display = 'block';
         } else {
             item.style.display = 'none';
         }
     });
 }
 
 function updateSelectedMembers() {
     const selectedContainer = document.getElementById('selected-members');
     const checkboxes = document.querySelectorAll('input[name="absentees[]"]:checked');
     
     // Clear existing tags
     selectedContainer.innerHTML = '';
     
     // Add tags for selected members
     checkboxes.forEach(function(checkbox) {
         const label = document.querySelector('label[for="' + checkbox.id + '"]');
         const memberName = label.textContent.trim();
         
         const tag = document.createElement('span');
         tag.className = 'selected-member-tag';
         tag.innerHTML = memberName + ' <span class="remove-tag" onclick="removeSelectedMember(\'' + checkbox.id + '\')">&times;</span>';
         
         selectedContainer.appendChild(tag);
     });
 }
 
 function removeSelectedMember(checkboxId) {
     const checkbox = document.getElementById(checkboxId);
     checkbox.checked = false;
     updateSelectedMembers();
 }
 
 // Presenter-specific functions
 function filterPresenters() {
     const searchTerm = document.getElementById('presenter-search').value.toLowerCase();
     const presenterItems = document.querySelectorAll('.presenter-item');
     
     presenterItems.forEach(function(item) {
         const memberName = item.getAttribute('data-name');
         if (memberName.includes(searchTerm)) {
             item.style.display = 'block';
         } else {
             item.style.display = 'none';
         }
     });
 }
 
 function updateSelectedPresenters() {
     const selectedContainer = document.getElementById('selected-presenters');
     const checkboxes = document.querySelectorAll('input[name="presenters[]"]:checked');
     
     // Clear existing tags
     selectedContainer.innerHTML = '';
     
     // Add tags for selected presenters
     checkboxes.forEach(function(checkbox) {
         const label = document.querySelector('label[for="' + checkbox.id + '"]');
         const memberName = label.textContent.trim();
         
         const tag = document.createElement('span');
         tag.className = 'selected-member-tag';
         tag.innerHTML = memberName + ' <span class="remove-tag" onclick="removeSelectedPresenter(\'' + checkbox.id + '\')">&times;</span>';
         
         selectedContainer.appendChild(tag);
     });
 }
 
 function removeSelectedPresenter(checkboxId) {
     const checkbox = document.getElementById(checkboxId);
     checkbox.checked = false;
     updateSelectedPresenters();
 }
 
 // Clear search when clicking outside
 document.addEventListener('click', function(event) {
     if (!event.target.closest('.custom-multiselect') && !event.target.closest('.search-members')) {
         // Optional: You can add any cleanup here
     }
 });
 </script>
                                <!-- script>
                                    // Initialize CKEditor for the textarea
                                    ClassicEditor.create(document.querySelector('#minutes'))
                                        .catch(error => {
                                            console.error('There was a problem initializing the editor:', error);
                                        });
                                </script -->
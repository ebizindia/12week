<?php
// Display session messages
if (isset($_SESSION['success_message'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success_message']); ?>
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
<?php 
unset($_SESSION['success_message']);
endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error_message']); ?>
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
<?php 
unset($_SESSION['error_message']);
endif; ?>
<style>
.needs-leads-cards{
	border:1px solid #ececec;
	border-radius:0 5px 5px 0;
}
.page-header {
  margin-bottom: 20px;
}
.page-header h4, .card-header h3{
	margin-bottom: 0;
	line-height: 30px;
	font-size:1.3rem !important;
}
.card-header {
  padding: 0.45rem 0.75rem;
}
textarea{resize: vertical !important;}
@media (max-width:583px){
	.card-border-none{box-shadow:none;padding:0;}	
}
</style>
<div>    	
	<div class="card mb-4 mt-3 card-border-none">
		<div class="card-body">
			<div class="row">
				<div class="col-12 mt-2">
					<div class="page-header">
						<h4><i class="fa fa-handshake-o"></i> Needs & Leads</h4>
						<p class="text-muted">Share your business needs and connect with leads from fellow members</p>
					</div>
				</div>
			</div>	
			<!-- Statistics Cards -->
			<div class="row">
				<div class="col-md-4 mb-3">
					<div class="card text-center border-left-primary">
						<div class="card-body needs-leads-cards">
							<div class="text-primary">
								<h2 class="font-weight-bold"><?php echo $this->body_template_data['statistics']['active_needs']; ?></h2>
							</div>
							<div class="text-muted">Active Requirements</div>
						</div>
					</div>
				</div>
				<div class="col-md-4 mb-3">
					<div class="card text-center border-left-success">
						<div class="card-body needs-leads-cards">
							<div class="text-success">
								<h2 class="font-weight-bold"><?php echo $this->body_template_data['statistics']['total_leads']; ?></h2>
							</div>
							<div class="text-muted">Total Leads</div>
						</div>
					</div>
				</div>
				<div class="col-md-4 mb-3">
					<div class="card text-center border-left-warning">
						<div class="card-body needs-leads-cards">
							<div class="text-warning">
								<h2 class="font-weight-bold"><?php echo $this->body_template_data['statistics']['active_members']; ?></h2>
							</div>
							<div class="text-muted">Members Active</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    <?php if ($this->body_template_data['permissions']['can_add_need']): ?>
    <!-- Post New Requirement Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fa fa-plus"></i> Post a New Requirement</h3>
                </div>
                <div class="card-body" style="padding-bottom: 15px;">
                    <form id="addNeedForm">
                        <div class="form-group">
                            <label for="needDescription" class="mt-2" style="max-width:100%;">Describe Your Requirement</label>
                            <textarea class="form-control" id="needDescription" name="description" rows="4" 
                                placeholder="Describe what you're looking for - services, products, partnerships, expertise, vendors, etc..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-paper-plane"></i> Post Requirement</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Current Requirements & Responses -->
    <div class="row">
        <div class="col-12 mb-4">           
			<div class="card-header bg-info text-white">
				<h3 class="mb-0"><i class="fa fa-list"></i> Current Requirements & Responses</h3>
			</div>
			<div id="needsContainer">
				<!-- Debug Info (remove after testing) -->
				<?php if (isset($_GET['debug'])): ?>
				<div class="alert alert-info">
					<strong>Debug Info:</strong><br>
					Current User ID: <?php echo $this->body_template_data['current_user']['id']; ?><br>
					Can Edit Own: <?php echo $this->body_template_data['permissions']['can_edit_own'] ? 'Yes' : 'No'; ?><br>
					Total Needs Found: <?php echo count($this->body_template_data['needs']); ?><br>
				</div>
				<?php endif; ?>
				
				<?php if (!empty($this->body_template_data['needs'])): ?>
					<?php foreach ($this->body_template_data['needs'] as $need): ?>
					<div class="need-item p-3 border rounded">
						<!-- Need Header -->
						<div class="need-header mb-3">
							<div class="d-flex justify-content-between align-items-start">
								<div>
									<h6 class="text-primary mb-1">
										<i class="fa fa-user"></i> <?php echo htmlspecialchars($need['fname'] . ' ' . $need['lname']); ?>
									</h6>
									<small class="text-muted">
										<i class="fa fa-clock-o"></i> <?php echo date('d M Y \a\t h:i A', strtotime($need['created_on'])); ?>
									</small>
								</div>
								<div class="d-flex align-items-center">
									<span class="badge badge-primary mr-2"><?php echo $need['leads_count']; ?> Response(s)</span>
									<?php if ($need['user_id'] == $this->body_template_data['current_user']['id'] && $this->body_template_data['permissions']['can_edit_own']): ?>
									<!-- Archive Form Button -->
									<form method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to archive this requirement? This will hide it from the main view.');">
										<input type="hidden" name="action" value="archive_need">
										<input type="hidden" name="need_id" value="<?php echo $need['id']; ?>">
										<button type="submit" class="btn btn-sm btn-outline-secondary" title="Archive this requirement">
											<i class="fa fa-archive"></i> Archive
										</button>
									</form>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<!-- Need Content -->
						<div class="need-content mb-3">
							<div class="p-3 bg-light border-left border-primary">
								<strong>Requirement:</strong> <?php echo nl2br(htmlspecialchars($need['description'])); ?>
							</div>
						</div>

						<!-- Leads/Responses -->
						<?php if (!empty($need['leads'])): ?>
						<div class="leads-section mb-3">
							<h6 class="text-success mb-2">
								<i class="fa fa-comments"></i> Leads & Responses (<?php echo count($need['leads']); ?>)
							</h6>
							<?php foreach ($need['leads'] as $lead): ?>
							<div class="lead-item mb-2 p-3 bg-success-light border-left border-success">
								<div class="d-flex justify-content-between align-items-start mb-2">
									<strong class="text-success">
										<?php echo htmlspecialchars($lead['fname'] . ' ' . $lead['lname']); ?>
									</strong>
									<small class="text-muted">
										<?php echo date('d M Y \a\t h:i A', strtotime($lead['created_on'])); ?>
									</small>
								</div>
								<div class="lead-response">
									<?php echo nl2br(htmlspecialchars($lead['response'])); ?>
								</div>
							</div>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>

						<!-- Add Response Form -->
						<?php if ($this->body_template_data['permissions']['can_add_lead']): ?>
						<div class="add-response-section">
							<button class="btn btn-sm btn-outline-success toggle-response-form" data-need-id="<?php echo $need['id']; ?>">
								<i class="fa fa-reply"></i> Submit Response
							</button>
							<div class="response-form mt-3" id="responseForm<?php echo $need['id']; ?>" style="display: none;">
								<form class="addLeadForm" data-need-id="<?php echo $need['id']; ?>">
									<div class="form-group">
										<textarea class="form-control" name="response" rows="3" 
											placeholder="Share your lead or recommendation..." required></textarea>
									</div>
									<button type="submit" class="btn btn-success btn-sm">
										<i class="fa fa-paper-plane"></i> Submit Response
									</button>
									<button type="button" class="btn btn-secondary btn-sm cancel-response">
										Cancel
									</button>
								</form>
							</div>
						</div>
						<?php endif; ?>
					</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="text-center py-5">
						<i class="fa fa-inbox fa-3x text-muted mb-3"></i>
						<h5 class="text-muted">No requirements posted yet</h5>
						<p class="text-muted">Be the first to post a business requirement!</p>
					</div>
				<?php endif; ?>
			</div>          
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
<div id="messageContainer" style="position: fixed; top: 20px; right: 20px; z-index: 1050;"></div>

<style>
.border-left-primary { border-left: 4px solid #007bff !important; }
.border-left-success { border-left: 4px solid #28a745 !important; }
.border-left-warning { border-left: 4px solid #ffc107 !important; }
.bg-success-light { background-color: #f8fff9 !important; }
.need-item { transition: box-shadow 0.3s ease; }
.need-item:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
.lead-item { background-color: #f8fff9; }
</style>
var meetfuncs={
	searchparams:[],  /* [{searchon:'',searchtype:'',searchtext:''},{},..] */
	sortparams:[],  /* [{sorton:'',sortorder:''},{},..] */
	default_sort:{sorton:'meet_date',sortorder:'DESC'},
	paginationdata:{},
	defaultleadtabtext:'Events',
	filtersapplied:[],
	statuschangestarted:0,
	ajax_data_script:'meetings.php',
	curr_page_hash:'',
	prev_page_hash:'',
	name_pattern: /^[A-Z0-9_ -]+$/i,
	int_pattern: /^\d+$/,
	gst_pattern: /^\d+(\.\d{1,2})?$/,
	pp_max_filesize:0,
	default_list: true,
	editorInstances: {},

	addRow: function () {
    var tableBody = document.getElementById("session-rows");
    var rowCount = tableBody.getElementsByTagName("tr").length;

    var newRow = document.createElement("tr");

    // Date input cell
    var dateCell = document.createElement("td");
    dateCell.innerHTML = `
      <div class="form-row">
        <div class="col">
          <input type="date" name="session_meet_date[]" id="session_meet_date_${rowCount}" placeholder="Date" class="form-control">
        </div>
        <div class="col-auto d-flex align-items-center">From</div>
        <div class="col">
          <input type="text" name="time_from[]" id="time_from_${rowCount}" placeholder="Start Time" class="form-control">
        </div>
        <div class="col-auto d-flex align-items-center">to</div>
        <div class="col">
          <input type="text" name="time_to[]" id="time_to_${rowCount}" placeholder="End Time" class="form-control">
        </div>
      </div>`;
    newRow.appendChild(dateCell);

    // Topic input cell
    var topicCell = document.createElement("td");
    topicCell.innerHTML = `
      <textarea name="topic[]" id="topic_${rowCount}" rows="3" placeholder="Enter Topic" class="form-control"></textarea>`;
    newRow.appendChild(topicCell);

    // Actions cell
    var actionsCell = document.createElement("td");
    actionsCell.innerHTML = `
      <button type="button" class="btn btn-danger delete-row" onclick="meetfuncs.deleteRow(this)"> - </button>`;
    newRow.appendChild(actionsCell);

    tableBody.appendChild(newRow);

    // Apply visibility logic to newly added row
    meetfuncs.toggleSessionMeetDateFields();
  },

  // New method to hide/show session_meet_date[] fields
 toggleSessionMeetDateFields: function () {
  const meetDate = document.getElementById('meet_date');
  const meetDateTo = document.getElementById('meet_date_to');
  const date1 = meetDate.value;
  const date2 = meetDateTo.value;
  const isSame = date1 && date2 && date1 === date2;

  const sessionDateInputs = document.querySelectorAll('input[name="session_meet_date[]"]');

  sessionDateInputs.forEach(function (input, index) {
    if (isSame) {
      input.value = date1;         // Set same date value
      input.style.display = 'none'; // Hide field
    } else {
      input.style.display = '';     // Show field
    }
  });
},




  // Function to delete a row
  deleteRow:function(button) {
    var row = button.closest("tr");
    row.remove();
  },
	
	initiateStatusChange:function(statuscell){
		var self=meetfuncs;

		var currtext=$(statuscell).find(':nth-child(1)').html();
		if($(statuscell).find(':nth-child(1)').hasClass('status-live')){
			var temptext='Deactivate';
			var color='#ff3333'; // red
		}else{
			var temptext='Activate';
			var color='#00a650'; // green
		}

		$(statuscell).find(':nth-child(1)').html(temptext);
		$(statuscell).find(':nth-child(1)').css('color',color);


	},
	toggleSearch: function(ev){
		var elem = $(ev.currentTarget);
		elem.toggleClass('search-form-visible', !elem.hasClass('search-form-visible'));
		$('#search_records').closest('.panel-search').toggleClass('d-none', !elem.hasClass('search-form-visible'));
		var search_form_cont = $('#search_records').closest('.panel-search');
		if(search_form_cont.hasClass('d-none'))
			elem.prop('title','Open search panel');
		else{
			elem.prop('title','Close search panel');
			$("#search-field_fullname").focus();
		}
		if (typeof(Storage) !== "undefined") {
			localStorage.event_search_toggle = elem.hasClass('search-form-visible') ? 'visible' : '';
		} else {
			Cookies.set('event_search_toggle', elem.hasClass('search-form-visible') ? 'visible' : '', {path : '/'/*, secure: true*/});
		}
	},

	confirmAndExecuteStatusChange:function(statuscell){
		var self=meetfuncs;

		self.statuschangestarted=1;
		var text=$(statuscell).find(':nth-child(1)').html();
		if($(statuscell).find(':nth-child(1)').hasClass('status-live')){
			var newstatus=0;
			var newstatustext='deactivate';
		}else{
			var newstatus=1;
			var newstatustext='activate';
		}

		var rowelem=$(statuscell).parent();
		var rowid=rowelem.attr('id');
		var temp=rowid.split('_');
		var userid=temp[temp.length-1];

		var fullname=rowelem.find('td:eq(1)').html();
		if(confirm("Really "+newstatustext+" the user \""+fullname+"\"?")){
			var options={cache:'no-cache',dataType:'json',async:true,type:'post',url:meetfuncs.ajax_data_script+"?mode=changeStatus",data:"newstatus="+newstatus+"&recordid="+userid,successResponseHandler:meetfuncs.handleStatusChangeResponse,successResponseHandlerParams:{statuscell:statuscell,rowelem:rowelem}};
			common_js_funcs.callServer(options);
			$(statuscell).removeClass("status-grn");
			$(statuscell).removeClass("status-red");
			if(parseInt(newstatus)==1){
				$(statuscell).addClass("status-grn");
			}else{
				$(statuscell).addClass("status-red");
			}
		}else{
			meetfuncs.statuschangestarted=0;
			meetfuncs.abortStatusChange(statuscell);

		}
	




	},

	abortStatusChange:function(statuscell){
		var self=meetfuncs;

		if(self.statuschangestarted==0){
			$(statuscell).find(':nth-child(1)').css('color','');
			if($(statuscell).find(':nth-child(1)').hasClass('status-live')){
				var temptext='Active';

			}else{
				var temptext='Inactive';

			}
			$(statuscell).find(':nth-child(1)').html(temptext);
		}
	},


	handleStatusChangeResponse:function(resp,otherparams){
		var self=meetfuncs;

		self.statuschangestarted=0;
		if(resp.errorcode!=0){

			self.abortStatusChange(otherparams.statuscell);
			if(resp.errorcode == 5)
				alert(resp.errormsg)
			else
				alert("Sorry, the status could not be updated.");

		}else{
			if($(otherparams.statuscell).find(':nth-child(1)').hasClass('status-live')){
				$(otherparams.statuscell).find(':nth-child(1)').removeClass('status-live').addClass("status-notlive");
			}else{
				$(otherparams.statuscell).find(':nth-child(1)').removeClass('status-notlive').addClass("status-live");
			}
			otherparams.rowelem.toggleClass('inactiverow');
			self.abortStatusChange(otherparams.statuscell);
		}

	},

	getList:function(options){
		var self=this;
		var pno=1;
		var params=[];
		if('pno' in options){
			params.push('pno='+encodeURIComponent(options.pno));
		}else{
			params.push('pno=1');
		}

		params.push('searchdata='+encodeURIComponent(JSON.stringify(self.searchparams)));
		params.push('sortdata='+encodeURIComponent(JSON.stringify(self.sortparams)));

		params.push('ref='+Math.random());

		$("#common-processing-overlay").removeClass('d-none');

		location.hash=params.join('&');


	},


	user_count:0,
	showList:function(resp,otherparams){
		//console.log(resp);
		var self=meetfuncs;
		var listhtml=resp[1].list;
		self.user_count=resp[1]['reccount'];
		$("#rec_list_container").removeClass('d-none');
		$("#rec_detail_add_edit_container").addClass('d-none');
		$("#common-processing-overlay").addClass('d-none');
		// $('#search_field').select2({minimumResultsForSearch: -1});
		$("#meetlistbox").html(listhtml);
		
		if(resp[1].tot_rec_cnt>0){
			$('#heading_rec_cnt').text((resp[1]['reccount']==resp[1]['tot_rec_cnt'])?`(${resp[1]['tot_rec_cnt']})`:`(${resp[1]['reccount'] || 0} of ${resp[1]['tot_rec_cnt']})`);
			
		}else{
			$('#heading_rec_cnt').text('(0)')
		}
			
		$("#add-record-button").removeClass('d-none');
		$("#refresh-list-button").removeClass('d-none');
		$(".back-to-list-button").addClass('d-none').attr('href',"meetings.php#"+meetfuncs.curr_page_hash);
		$("#edit-record-button").addClass('d-none');
		self.paginationdata=resp[1].paginationdata;

		self.setSortOrderIcon();


	},


	onListRefresh:function(resp,otherparams){
		var self=meetfuncs;
		$("#common-processing-overlay").addClass('d-none');
		var listhtml=resp[1].list;
		$("#meetlistbox").html(listhtml);
		self.paginationdata=resp[1].paginationdata;
		self.setSortOrderIcon();
	},

	setExportLink: function(show){
		const dnld_elem = $('#export_members');
		if(dnld_elem.length<=0) // the download link element does not exist, the user might not be in ADMIN role
			return;
		let url = '#';
		if(show===true){
			let params = [];
			params.push('mode=export');
			params.push('searchdata='+encodeURIComponent(JSON.stringify(this.searchparams)));
			params.push('sortdata='+encodeURIComponent(JSON.stringify(this.sortparams)));
			params.push('ref='+Math.random());
			url = `${window.location.origin}${window.location.pathname}?${params.join('&')}`;
			
		}
		dnld_elem.attr('href',url).toggleClass('d-none', show!==true);
	},


	expandFilterBox:function(){
		var self=meetfuncs;
		document.leadsearchform.reset();
		for(var i=0; i<self.searchparams.length; i++){
			switch(self.searchparams[i].searchon){
				case 'name': $("#fullname").val(self.searchparams[i].searchtext[0]); break;

				case 'email': $("#email").val(self.searchparams[i].searchtext[0]); break;

				case 'usertype': $("#usertype").val(self.searchparams[i].searchtext[0]); break;


			}

		}
		$("#searchbox").show();
		$("#applyfilter").hide();

	},


	collapseFilterBox:function(){
		var self=meetfuncs;
		$("#searchbox").hide();
		if($("#filterstatus").is(':hidden')){
			$("#applyfilter").show();
			$("#filterstatus").hide();
		}else{
			$("#filterstatus").show();
			$("#applyfilter").hide();

		}
		return false;
	},

	onDateFilterChange:function(elem){
		var date_filtertype=$(elem).val();
		if(date_filtertype=='EQUAL'){
			$("#enddateboxcont").hide();
			$("#enddate").val('');
			$("#startdate").val('')
			$("#startdateboxcont").show();

		}else if(date_filtertype=='BETWEEN'){
			$("#enddateboxcont").show();
			$("#enddate").val('');
			$("#startdate").val('')
			$("#startdateboxcont").show();
		}else{
			$("#enddate").val('');
			$("#startdate").val('')
			$("#enddateboxcont").hide();
			$("#startdateboxcont").hide();
		}

	},


	resetSearchParamsObj:function(){
		var self=meetfuncs;
		self.searchparams=[];
	},

	setSearchParams:function(obj){
		var self=meetfuncs;
		self.searchparams.push(obj);

	},

	clearSearch:function(e){
		let remove_all = true;
		if(e){
			e.stopPropagation();
			elem = e.currentTarget;
			if($(elem).hasClass('remove_filter')){
				remove_all = $(elem).data('fld');
				$(elem).parent('.searched_elem').remove();
				if(remove_all==='falls_in_period'){
					$("#search-field_periodstart_picker").datepicker('setDate', null);
					$("#search-field_periodend_picker").datepicker('setDate', null);
				}else
					$('.panel-search .srchfld[data-fld='+remove_all+']').val('');
			}
		}

		var self=meetfuncs;
		// self.filtersapplied=[]; // remove the filter bar messages
		if(remove_all===true){
			self.resetSearchParamsObj();
			document.search_form.reset();
			$("#search-field_periodstart_picker").datepicker('setDate', null);
			$("#search-field_periodend_picker").datepicker('setDate', null);
		}else{
			self.searchparams = self.searchparams.filter(fltr=>{
				return fltr.searchon !== remove_all;
			});
		}
		var options={pno:1};
		self.getList(options);
		return false;
	},


	doSearch:function(){

		meetfuncs.resetSearchParamsObj();
		let period_text = ['',''];
		let period = false;
		let fld = '';
		$('.panel-search .srchfld').each(function(i, el){
			let val = $.trim($(el).val());
			if(val!=''){
				fld = $(el).data('fld');
				if(fld=='period_start')
					period_text[0] = val;
				else if(fld=='period_end')
					period_text[1] = val;
				else
					meetfuncs.setSearchParams({searchon:$(el).data('fld'),searchtype:$(el).data('type'),searchtext:val});
			

			}
		});

		if(period_text[0]!='' || period_text[1]!='')
			meetfuncs.setSearchParams({searchon:'falls_in_period',searchtype:'CONTAINS',searchtext:period_text});

		if(meetfuncs.searchparams.length<=0){
			if($('.clear-filter').length>0)
				$('.clear-filter').trigger('click');
			return false;
		}

		var options={pno:1};
		meetfuncs.getList(options);
		//self.toggleSearch(this);
		return false;
	},


	changePage:function(ev){
		ev.preventDefault();
		if(!$(ev.currentTarget).parent().hasClass('disabled')){
			var self=meetfuncs;
			var pno=$(ev.currentTarget).data('page');
			self.getList({pno:pno});
			// return false;
		}

	},



	sortTable:function(e){
		var self=e.data.self;

		var elemid=e.currentTarget.id;
		var elemidparts=elemid.split('_');
		var sorton=elemidparts[1].replace(/-/g,'_');
		var sortorder='ASC';

		if(sorton == 'usertype')
			sorton = 'user_type';

		if($(e.currentTarget).find("i:eq(0)").hasClass('fa-sort-up')){
			sortorder='DESC';
		}

		var pno = 1;
	

		meetfuncs.sortparams=[];
		meetfuncs.sortparams.push({sorton:sorton, sortorder:sortorder});
		var options={pno:pno};
		meetfuncs.getList(options);

	},



	setSortOrderIcon:function(){
		var self=meetfuncs;
		if(self.sortparams.length>0){
			var sorton = self.sortparams[0].sorton == 'user_type'?'usertype':self.sortparams[0].sorton.replace(/_/g,'-');
			var colheaderelemid='colheader_'+sorton;

			if(self.sortparams[0].sortorder=='DESC'){
				var sort_order_class='fa-sort-down';
			}else{
				var sort_order_class='fa-sort-up';
			}
			$("#"+colheaderelemid).siblings('th.sortable').removeClass('sorted-col').find('i:eq(0)').removeClass('fa-sort-down fa-sort-up').addClass('fa-sort').end().end().addClass('sorted-col').find('i:eq(0)').removeClass('fa-sort-down fa-sort-up').addClass(sort_order_class);


		}
	},



	openRecordForViewing:function(recordid){
		var self=meetfuncs;
		if(recordid=='')
			return false;

		$("#record-save-button").addClass('d-none').attr('disabled', 'disabled');
		$("#common-processing-overlay").removeClass('d-none');
		var coming_from='';
		var options={mode:'viewrecord',recordid:recordid,loadingmsg:"Opening the lead '"+recordid+"' for viewing...",leadtabtext:'View Event Details',coming_from:coming_from}
		self.openRecord(options);
		 return false;

	},

	openRecordForEditing:function(recordid){

		//alert(recordid);
		var self=meetfuncs;
		if(recordid=='')
			return false;

		document.addmeetform.reset();
		$(".form-control").removeClass("error-field");
		$("#record-save-button").removeClass('d-none').attr('disabled', false);
		// $("#add_form_field_role").next('.select2-container').removeClass("error-field");
		$("#common-processing-overlay").removeClass('d-none');
		$("#record-add-cancel-button").attr('href',"meetings.php#"+meetfuncs.prev_page_hash);
		$('#msgFrm').removeClass('d-none');
		var coming_from='';//elem.data('in-mode');
		var options={mode:'editrecord',recordid:recordid,leadtabtext:'Edit Event\'s Details',coming_from:coming_from}
		self.openRecord(options);
		return false;

	},


	openRecord:function(options){
		var self=meetfuncs;
		var opts={leadtabtext:'Event Details'};
		$.extend(true,opts,options);

		meetfuncs.dep_rowno_max=-1;

		var params={mode:"getRecordDetails",recordid:opts.recordid};
		var options={cache:'no-cache',async:true,type:'post',dataType:'json',url:self.ajax_data_script,params:params,successResponseHandler:self.showLeadDetailsWindow,successResponseHandlerParams:{self:self,mode:opts.mode,recordid:opts.recordid,coming_from:opts.coming_from,header_bar_text:opts.leadtabtext}};
		common_js_funcs.callServer(options);

	},


	showLeadDetailsWindow1:function(resp,otherparams){

		console.log(resp);

		const self=otherparams.self;
		let container_id='';
		$("#common-processing-overlay").addClass('d-none');
		const rec_id= resp[1].record_details.id ??''; // ad_banners table's id
		
		if(otherparams.mode=='editrecord'){
			var coming_from=otherparams.coming_from;


			if(rec_id!=''){

				if(resp[1].can_edit===false){
					// User is not authorised to edit this record so send him back to the previous screen
					location.hash=meetfuncs.prev_page_hash;
					return;
				}

				meetfuncs.removeEditRestrictions();

				let meet_date = resp[1].record_details.meet_date || '';
				let meet_date_to = resp[1].record_details.meet_date_to || '';
				let meet_time = resp[1].record_details.meet_time || '';
				let meet_title = resp[1].record_details.meet_title??'';
				let venue = resp[1].record_details.venue??'';
				let active = resp[1].record_details.active || '';
				let minutes = resp[1].record_details.minutes || '';
				const today_obj = new Date(resp[1].today);

				ClassicEditor.create(document.querySelector('#minutes')).then(editor => {
		        // Store the editor instance globally in meetfuncs
		        meetfuncs.editorInstances.minutes = editor;
		    })
		    .catch(error => {
		        console.error('There was a problem initializing the editor:', error);
		    });
				

				var contobj=$("#rec_detail_add_edit_container");

				$('.alert-danger').addClass('d-none').find('.alert-message').html('');
				$('#msgFrm').removeClass('d-none');
				contobj.find(".form-actions").removeClass('d-none');

				contobj.find("form[name=addmeetform]:eq(0)").data('mode','edit-rec').find('input[name=status]').attr('checked',false).end().get(0).reset();

				contobj.find("#add_edit_mode").val('updaterec');
				contobj.find("#add_edit_recordid").val(rec_id);
				contobj.find("#meet_date").val(meet_date);
				contobj.find("#meet_date_to").val(meet_date_to);
				contobj.find("#meet_time").val(meet_time);
				contobj.find("#meet_title").val(meet_title);
				contobj.find("#venue").val(venue);
				//contobj.find("#minutes").val(minutes);

				// Initialize CKEditor if not already initialized
            if (!meetfuncs.editorInstances.minutes) {
                ClassicEditor.create(document.querySelector('#minutes'))
                    .then(editor => {
                        // Store the editor instance globally in meetfuncs
                        meetfuncs.editorInstances.minutes = editor;

                        // Pre-fill the CKEditor with the 'minutes' value
                        editor.setData(minutes);
                    })
                    .catch(error => {
                        console.error('There was a problem initializing the editor:', error);
                    });
            } else {
                // If already initialized, just update the content
                meetfuncs.editorInstances.minutes.setData(minutes);
            }

				//New Add				
				meetfuncs.populateAgendaRows(resp);
				//End new Add
				
				

				let header_text = 'Edit Meeting';
				
				contobj.find("#record-add-cancel-button").data('back-to',coming_from);
				contobj.find("#record-save-button>span:eq(0)").html('Save Changes');
				contobj.find("#panel-heading-text").text(header_text);
				contobj.find("#infoMsg").html('Edit Meeting');
				meetfuncs.setheaderBarText(header_text);

				meetfuncs.applyEditRestrictions(resp[1].edit_restricted_fields);
				container_id='rec_detail_add_edit_container';





			}else{

				var message="Sorry, the edit window could not be opened (Server error).";
				if(resp[0]==1){
					message="Sorry, the edit window could not be opened (User ID missing).";
				}else if(resp[0]==2){
					message="Sorry, the edit window could not be opened (Server error).";
				}else if(resp[0]==3){
					message="Sorry, the edit window could not be opened (Invalid user ID).";
				}

				alert(message);
				location.hash=meetfuncs.prev_page_hash;
				return;

			}

		}

		if(container_id!=''){
			$(".back-to-list-button").removeClass('d-none');
			$("#refresh-list-button").addClass('d-none');
			$("#add-record-button").addClass('d-none');
			$("#rec_list_container").addClass('d-none');

			if(container_id!='rec_detail_add_edit_container'){
				$("#rec_detail_add_edit_container").addClass('d-none');
				$("#edit-record-button").removeClass('d-none').data('recid',otherparams.recordid);
			}else if(container_id!='user_detail_view_container'){
				$("#user_detail_view_container").addClass('d-none');
				$("#edit-record-button").addClass('d-none');
			}

			$("#"+container_id).removeClass('d-none');
			self.setheaderBarText(otherparams.header_bar_text);

		}

		

	},
showLeadDetailsWindow: function (resp, otherparams) {
    console.log(resp);
    $("#minutesBlock").removeClass("d-none");
    const self = otherparams.self;
    let container_id = '';
    $("#common-processing-overlay").addClass('d-none');
    const rec_id = resp[1].record_details.id ?? ''; // ad_banners table's id

    if (otherparams.mode == 'editrecord') {
        const coming_from = otherparams.coming_from;

        if (rec_id != '') {
            if (resp[1].can_edit === false) {
                // User is not authorised to edit this record, so redirect to the previous screen
            	  $("#download-agenda-btn").hide();
                location.hash = meetfuncs.prev_page_hash;
                return;
            }

            $("#download-agenda-btn").show();

            meetfuncs.removeEditRestrictions();

            let meet_date = resp[1].record_details.meet_date || '';
            let meet_date_to = resp[1].record_details.meet_date_to || '';
            let meet_time = resp[1].record_details.meet_time || '';
            let meet_title = resp[1].record_details.meet_title || '';
            let venue = resp[1].record_details.venue || '';
            let active = resp[1].record_details.active || '';
            let minutes = resp[1].record_details.minutes || '';
            const today_obj = new Date(resp[1].today);

            const contobj = $("#rec_detail_add_edit_container");

            // Reset and populate form fields
            $('.alert-danger').addClass('d-none').find('.alert-message').html('');
            $('#msgFrm').removeClass('d-none');
            contobj.find(".form-actions").removeClass('d-none');
            contobj.find("form[name=addmeetform]:eq(0)").data('mode', 'edit-rec').find('input[name=status]').attr('checked', false).end().get(0).reset();

            contobj.find("#add_edit_mode").val('updaterec');
            contobj.find("#add_edit_recordid").val(rec_id);
            contobj.find("#meet_date").val(meet_date);
            contobj.find("#meet_date_to").val(meet_date_to);
            contobj.find("#meet_time").val(meet_time);
            contobj.find("#meet_title").val(meet_title);
            contobj.find("#venue").val(venue);

            // CKEditor initialization and pre-fill logic
            if (!meetfuncs.editorInstances.minutes) {
                // Initialize CKEditor if not already initialized
                ClassicEditor.create(document.querySelector('#minutes'))
                    .then(editor => {
                        // Store the editor instance globally in meetfuncs
                        meetfuncs.editorInstances.minutes = editor;

                        // Pre-fill CKEditor with the 'minutes' value
                        editor.setData(minutes);
                    })
                    .catch(error => {
                        console.error('There was a problem initializing the editor:', error);
                    });
            } else {
                // If CKEditor is already initialized, just update the content
                meetfuncs.editorInstances.minutes.setData(minutes);
            }

            // Continue populating other fields
            meetfuncs.populateAgendaRows(resp);

            if(resp[1].absentees_details){console.log(resp[1].absentees_details)
                resp[1].absentees_details.forEach((a, index) => {
                    document.getElementById('member_'+a.user_id).click();
                });
            }
            if(resp[1].presenters_details){console.log(resp[1].presenters_details)
                resp[1].presenters_details.forEach((p, index) => {
                    document.getElementById('presenter_'+p.user_id).click();
                });
            }
            let header_text = 'Edit Meeting';
            contobj.find("#record-add-cancel-button").data('back-to', coming_from);
            contobj.find("#record-save-button>span:eq(0)").html('Save Changes');
            contobj.find("#panel-heading-text").text(header_text);
            contobj.find("#infoMsg").html('Edit Meeting');
            meetfuncs.setheaderBarText(header_text);

            meetfuncs.applyEditRestrictions(resp[1].edit_restricted_fields);
            container_id = 'rec_detail_add_edit_container';
        } else {
            $("#download-agenda-btn").hide();
            var message = "Sorry, the edit window could not be opened (Server error).";
            if (resp[0] == 1) {
                message = "Sorry, the edit window could not be opened (User ID missing).";
            } else if (resp[0] == 2) {
                message = "Sorry, the edit window could not be opened (Server error).";
            } else if (resp[0] == 3) {
                message = "Sorry, the edit window could not be opened (Invalid user ID).";
            }



            alert(message);
            location.hash = meetfuncs.prev_page_hash;
            return;
        }
    }

    if (container_id != '') {
        $(".back-to-list-button").removeClass('d-none');
        $("#refresh-list-button").addClass('d-none');
        $("#add-record-button").addClass('d-none');
        $("#rec_list_container").addClass('d-none');

        if (container_id != 'rec_detail_add_edit_container') {
            $("#rec_detail_add_edit_container").addClass('d-none');
            $("#edit-record-button").removeClass('d-none').data('recid', otherparams.recordid);
        } else if (container_id != 'user_detail_view_container') {
            $("#user_detail_view_container").addClass('d-none');
            $("#edit-record-button").addClass('d-none');
        }

        $("#" + container_id).removeClass('d-none');
        self.setheaderBarText(otherparams.header_bar_text);
    }
},

recordForDelete:function(recordid){

		//alert(recordid);
		var self=meetfuncs;
		if(recordid=='')
			return false;

		var coming_from='';//elem.data('in-mode');
		

		var params={mode:"deleterec",recordid:recordid};
		var options={cache:'no-cache',async:true,type:'post',dataType:'json',url:self.ajax_data_script,params:params,successResponseHandler:self.handleDeleteResponse};
		common_js_funcs.callServer(options);

	},
	handleDeleteResponse:function(resp)
	{
		alert(resp.message);
		if(resp.error_code==0)
		{
			
			location.hash=meetfuncs.prev_page_hash;
		}
		
	},

	populateAgendaRows: function(resp) {
    const agendaDetails = resp[1].agenda_record_details;
    const tableBody = document.getElementById("session-rows");

    // Clear existing rows (except the initial placeholders if any)
    tableBody.innerHTML = '';

    // Iterate over agenda details and populate rows
    agendaDetails.forEach((agenda, index) => {
        const newRow = document.createElement("tr");

        // Time inputs cell
        const dateCell = document.createElement("td");
        dateCell.innerHTML = `
            <div class="form-row">
        				 <div class="col">
                    <input type="date" name="session_meet_date[]" id="session_meet_date_${index}" value="${agenda.session_meet_date || ''}" placeholder="Start Time" class="form-control">
                </div>
        				<div class="col-auto d-flex align-items-center">From</div>
                <div class="col">
                    <input type="text" name="time_from[]" id="time_from_${index}" value="${agenda.time_from || ''}" placeholder="Start Time" class="form-control">
                </div>
                <div class="col-auto d-flex align-items-center">to</div>
                <div class="col">
                    <input type="text" name="time_to[]" id="time_to_${index}" value="${agenda.time_to || ''}" placeholder="End Time" class="form-control">
                </div>
            </div>
            <input type="hidden" name="agenda_id[]" value="${agenda.id || ''}">
        `;
        newRow.appendChild(dateCell);

        // Topic input cell
        const topicCell = document.createElement("td");
        topicCell.innerHTML = `
            <textarea name="topic[]" id="topic_${index}" rows="3" placeholder="Enter Topic" class="form-control">${agenda.topic || ''}</textarea>
        `;
        newRow.appendChild(topicCell);

        // Actions cell
        const actionsCell = document.createElement("td");
        actionsCell.innerHTML = `
            <button type="button" class="btn btn-danger delete-row" onclick="meetfuncs.deleteRow(this)"> - </button>
        `;
        newRow.appendChild(actionsCell);

        // Append row to the table body
        tableBody.appendChild(newRow);
    });

    // If there are no agenda details, add a single empty row for user input
    if (agendaDetails.length === 0) {
        meetfuncs.addRow();
    }
},


	applyEditRestrictions: function(restricted_fields){
		const contobj=$("#rec_detail_add_edit_container");
		restricted_fields.forEach(fld=>{
			switch(fld){
				case 'name':
					contobj.find("#add_form_field_name").prop('disabled', restricted_fields.includes('name')).addClass('rstrctedt');
					break;
				case 'active':
					contobj.find("input[name=active]").prop('disabled', restricted_fields.includes('active')).addClass('rstrctedt');
					break;
				case 'dsk_img':
					contobj.find("#add_form_field_dskimg").prop('disabled', restricted_fields.includes('dsk_img')).addClass('rstrctedt');
					break;
			}

		});
	},

	removeEditRestrictions: function(){
		const contobj=$("#rec_detail_add_edit_container");
		contobj.find("#add_form_field_name, input[name=active], #add_form_field_dskimg").prop('disabled', false).end();			
		contobj.find('.rstrctedt').removeClass('rstrctedt');	
	},


	
	backToList:function(e){
	},

	agendaForPrint:function(recordid){

		alert(recordid);
	/*	var self=meetfuncs;
		if(recordid=='')
			return false;

		var coming_from='';//elem.data('in-mode');
		

		var params={mode:"deleterec",recordid:recordid};
		var options={cache:'no-cache',async:true,type:'post',dataType:'json',url:self.ajax_data_script,params:params,successResponseHandler:self.handlePrintAgenda};
		common_js_funcs.callServer(options);
		*/

	},
	handlePrintAgenda:function(resp)
	{
		alert(resp.message);
		if(resp.error_code==0)
		{
			
			location.hash=meetfuncs.prev_page_hash;
		}
		
	},


	refreshList:function(e){
		if(typeof e=='object' && e.hasOwnProperty('data') && e.data.hasOwnProperty('self')){
			var self=e.data.self;
		}else{
			var self=meetfuncs;
		}

		var currpage=self.paginationdata.curr_page;

		var options={pno:currpage,successResponseHandler:self.onListRefresh};
		self.getList(options);
		return false;

	},


	handleAddRecResponse:function(resp){
		var self=meetfuncs;
		$(".form-control").removeClass("error-field");

		if(resp.error_code==0){
			var message_container = '.alert-success';
			$("#record-add-cancel-button>i:eq(0)").next('span').html('Close');
			$("form[name=addmeetform]").find(".error-field").removeClass('error-field').end().get(0).reset();
			$("#add_form_field_status_y").prop('checked',true);
			$("#add_form_field_regactive_y").prop('checked',true);
			$('#dsk_banner_img').attr('src',""); // empty the dsk image src
			$('#mob_banner_img').attr('src',""); // empty the mob image src
			$('#add_form_field_dskimg').val(''); // empty the dsk img file input
			$('#add_form_field_mobimg').val(''); // empty the mob img file input
			$("#add_form_field_startdt_picker").datepicker('setDate', null);
			$("#add_form_field_enddt_picker").datepicker('setDate', null);
			$("#add_form_field_regstartdt_picker").datepicker('setDate', null);
			$("#add_form_field_regenddt_picker").datepicker('setDate', null);
			$("#add_form_field_name").focus();

			document.querySelector('.main-content').scrollIntoView(true);
		}else if(resp.error_code==2){
			var message_container ='';
			if(resp.error_fields.length>0){
				var msg = resp.message;
				alert(msg);
				$(resp.error_fields[0]).focus();
				$(resp.error_fields[0]).addClass("error-field");
			}

		}else{
			var message_container = '.alert-danger';
		}

		$('#record-save-button, #record-add-cancel-button').removeClass('disabled').attr('disabled',false);
		$("#common-processing-overlay").addClass('d-none');

		if(message_container!=''){
			$(message_container).removeClass('d-none').siblings('.alert').addClass('d-none').end().find('.alert-message').html(resp.message);
			var page_scroll='.main-container-inner';
			common_js_funcs.scrollTo($(page_scroll));
			$('#msgFrm').addClass('d-none');
		}
	},

	handleUpdateRecResponse:function(resp){
		var self=meetfuncs;

		var mode_container='rec_detail_add_edit_container';
		$(".form-control").removeClass("error-field");

		if(resp.error_code==0){
			
			var message_container = '.alert-success';

			// Update the dsk and mob images if required
			if(resp.other_data.dsk_img_url && resp.other_data.dsk_img_url!=''){
				// if(resp.other_data.dsk_img_org_width < resp.other_data.dsk_img_max_width)
					$('#dsk_banner_img').attr({width:resp.other_data.dsk_img_org_width, src:resp.other_data.dsk_img_url}).parent('.ad_banner_image').removeClass('d-none').end();
				// else
				// 	$('#dsk_banner_img').attr({width:'', src:resp.other_data.dsk_img_url}).parent('.ad_banner_image').removeClass('d-none').end();
			}else{
				$('#dsk_banner_img').parent('.ad_banner_image').toggleClass('d-none', $('#dsk_banner_img').attr('src')=='');
			}

			if(resp.other_data.mob_img_url && resp.other_data.mob_img_url!=''){
				// if(resp.other_data.mob_img_org_width < resp.other_data.mob_img_max_width)
					$('#mob_banner_img').attr({width:resp.other_data.mob_img_org_width, src:resp.other_data.mob_img_url}).parent('.ad_banner_image').removeClass('d-none').end();
				// else
				// 	$('#mob_banner_img').attr({width:'', src:resp.other_data.mob_img_url}).parent('.ad_banner_image').removeClass('d-none').end();
			}else {
				$('#mob_banner_img').parent('.ad_banner_image').toggleClass('d-none', $('#mob_banner_img').attr('src')=='');
			}
			
			$('#add_form_field_dskimg, #add_form_field_mobimg').val('');
			
			$("#add_form_field_name").focus();
		}else if(resp.error_code==2){
			// data validation errors

			var message_container ='';

			if(resp.error_fields.length>0){
				alert(resp.message);
				setTimeout(()=>{$(resp.error_fields[0]).addClass("error-field").focus(); },0);

			}

		}else{
			var message_container = '.alert-danger';
		}

		$('#record-save-button, #record-add-cancel-button').removeClass('disabled').attr('disabled',false);
		$("#common-processing-overlay").addClass('d-none');
		if(message_container!=''){
			$(message_container).removeClass('d-none').siblings('.alert').addClass('d-none').end().find('.alert-message').html(resp.message);//.end().delay(3000).fadeOut(800,function(){$(this).css('display','').addClass('d-none');});
			var page_scroll='.main-container-inner';
			common_js_funcs.scrollTo($(page_scroll));
			$('#msgFrm').addClass('d-none');
		}

	},

	saveRecDetails:function(formelem){
	//	alert("saveRecDetails");
		var self=meetfuncs;
		var data_mode=$(formelem).data('mode');

		var res = self.validateRecDetails({mode:data_mode});
		if(res.error_fields.length>0){

			alert(res.errors[0]);
			setTimeout(function(){
				$(res.error_fields[0],'#addmeetform').focus();
			},0);
			return false;

		}

		$("#common-processing-overlay").removeClass('d-none');
		$('#record-save-button, #record-add-cancel-button').addClass('disabled').attr('disabled',true);
		$('#rec_detail_add_edit_container .error-field').removeClass('error-field');

		return true;

	},


	validateRecDetails: function(opts) {
	//	alert("validateRecDetails");
    var errors = [], error_fields = [];
    let mode = 'add-rec';

    $(".form-control").removeClass("error-field");

    if (typeof opts == 'object' && opts.hasOwnProperty('mode')) {
        mode = opts.mode;
    }

    const frm = $('#addmeetform');

    // Validate the mandatory fields: meet_date, meet_time, venue
    let meetDate = $.trim(frm.find('input[name="meet_date"]').val());
    let meetDateTo = $.trim(frm.find('input[name="meet_date_to"]').val());
    let meetTime = $.trim(frm.find('input[name="meet_time"]').val());
    let meetTitle = $.trim(frm.find('input[name="meet_title"]').val());
    let venue = $.trim(frm.find('input[name="venue"]').val());

    if (!meetDate) {
        errors.push('Meeting start date is required.');
        error_fields.push('input[name="meet_date"]');
        frm.find('input[name="meet_date"]').addClass("error-field");
    }

    if (!meetDateTo) {
        errors.push('Meeting end date is required.');
        error_fields.push('input[name="meet_date_to"]');
        frm.find('input[name="meet_date_to"]').addClass("error-field");
    }

    if (meetDate !== "" && meetDateTo !== "") {
    let dateFrom = new Date(meetDate);
    let dateTo = new Date(meetDateTo);

	    if (dateTo < dateFrom) {
	    		errors.push('Meeting end date must be the same as or after the start date');
        	error_fields.push('input[name="meet_date_to"]');
        	frm.find('input[name="meet_date_to"]').addClass("error-field");
	       
	    }
		}


    if (!meetTime) {
        errors.push('Meeting time is required.');
        error_fields.push('input[name="meet_time"]');
        frm.find('input[name="meet_time"]').addClass("error-field");
    }

    if (!meetTitle) {
        errors.push('Meeting Title is required.');
        error_fields.push('input[name="meet_title"]');
        frm.find('input[name="meet_title"]').addClass("error-field");
    }
    if (!venue) {
        errors.push('Venue is required.');
        error_fields.push('input[name="venue"]');
        frm.find('input[name="venue"]').addClass("error-field");
    }

    // Additional validation logic for session rows
    frm.find('#session-rows tr').each(function() {
        let row = $(this);
        let sessionMeetDate = $.trim(row.find('input[name="session_meet_date[]"]').val());
        let startTime = $.trim(row.find('input[name="time_from[]"]').val());
        let endTime = $.trim(row.find('input[name="time_to[]"]').val());
        //let topic = $.trim(row.find('input[name="topic[]"]').val());
        let topic = $.trim(row.find('textarea[name="topic[]"]').val());

        if (sessionMeetDate || startTime || endTime || topic) {
            if (!sessionMeetDate) {
                errors.push('Date is required when end time or topic is provided.');
                error_fields.push('input[name="session_meet_date[]"]');
                row.find('input[name="session_meet_date[]"]').addClass("error-field");
            }

            if (!startTime) {
                errors.push('Start time is required when end time or topic is provided.');
                error_fields.push('input[name="time_from[]"]');
                row.find('input[name="time_from[]"]').addClass("error-field");
            }

           /*  if (!endTime) {
                errors.push('End time is required when start time or topic is provided.');
                error_fields.push('input[name="time_to[]"]');
                row.find('input[name="time_to[]"]').addClass("error-field");
            } */

            if (!topic) {
                errors.push('Topic is required when start time or end time is provided.');
                error_fields.push('input[name="topic[]"]');
                row.find('input[name="topic[]"]').addClass("error-field");
            }
        }
    });

    // Return the errors and error fields
    return {'errors': errors, 'error_fields': error_fields};
},

	openAddUserForm:function(e){

		if(typeof e=='object' && e.hasOwnProperty('data') && e.data.hasOwnProperty('self')){
			var self=e.data.self;
		}else{
			var self=meetfuncs;
		}
		document.addmeetform.reset();
		
		//meetfuncs.removeEditRestrictions();

		meetfuncs.dep_rowno_max=-1;
		$(".form-control").removeClass("error-field");
		$("#refresh-list-button").addClass('d-none');
		$("#add-record-button").addClass('d-none');
		$("#edit-record-button").addClass('d-none');
		$("#rec_list_container").addClass('d-none');
		$("#rec_detail_add_edit_container").removeClass('d-none').find("#panel-heading-text").text('Create Meeting').end();
		$('#msgFrm').removeClass('d-none');
			
		$(".back-to-list-button").removeClass('d-none');
		$("#rec_detail_add_edit_container").find("#record-save-button>span:eq(0)").html('Add New Meeting').end().find("#add_edit_mode").val('createrec').end().find("#add_edit_recordid").val('').end().find("#record-add-cancel-button").data('back-to','').attr('href',"meetings.php#"+meetfuncs.prev_page_hash);
		$("form[name=addmeetform]").data('mode','add-rec').find(".error-field").removeClass('error-field').end().find('input[name=active]').attr('checked',false).end().get(0).reset();

				
		self.setheaderBarText("");
		$('#meet_date').focus();
		
		document.querySelector('.main-content').scrollIntoView(true);
		return false;
	},

	openAddMemberEditForm:function(e){

		if(typeof e=='object' && e.hasOwnProperty('data') && e.data.hasOwnProperty('self')){
			var self=e.data.self;
		}else{
			var self=meetfuncs;
		}
		document.addmeetform.reset();

		//meetfuncs.removeEditRestrictions();

		meetfuncs.dep_rowno_max=-1;
		$(".form-control").removeClass("error-field");
		$("#minutesBlock").removeClass("d-none");
		$("#refresh-list-button").addClass('d-none');
		$("#add-record-button").addClass('d-none');
		$("#edit-record-button").addClass('d-none');
		$("#user_list_container").addClass('d-none');
		$("#user_detail_view_container").addClass('d-none');
		$("#user_detail_add_edit_container").removeClass('d-none').find("#panel-heading-text").text('Edit Meeting').end();
		
		
		$('#msgFrm').removeClass('d-none');
			
		$(".back-to-list-button").removeClass('d-none');
		//$("#add_password_msg").removeClass('d-none');
		//$("#edit_password_msg").addClass('d-none');

		$("#user_detail_add_edit_container").find("#record-save-button").removeClass('d-none disabled').attr('disabled', false).find("span:eq(0)").html('Update Meeting').end().end().find("#add_edit_mode").val('createrec').end().find("#add_edit_recordid").val('').end().find("#add_edit_usertype").val('').end().find("#record-add-cancel-button").data('back-to','').attr('href',"meeting.php#"+meetfuncs.prev_page_hash);
		$("form[name=addmeetform]").data('mode','add-user').find(".error-field").removeClass('error-field').end().find('input[name=status]').attr('checked',false).end().get(0).reset();

		

	},
	deleteUser:function(ev){
		var elem = $(ev.currentTarget);
		var id =elem.data('recid');
		// alert(id);
		if(confirm('Do you want to delete this user?')){

			var rec_details = {};
			common_js_funcs.callServer({cache:'no-cache',async:false,dataType:'json',type:'post',url:meetfuncs.ajax_data_script,params:{mode:'deleteUser', user_id:id},
				successResponseHandler:function(resp,status,xhrobj){
					if(resp.error_code == 0)
						meetfuncs.handleDeleteResp(resp);
					else
						alert(resp.message);
				},
				successResponseHandlerParams:{}});
			return rec_details;
		}

	},
	handleDeleteResp:function(resp){
		// console.log(resp);return false;
		alert(resp.message);
		meetfuncs.refreshList();
	},

	closeAddUserForm:function(){
		var self =this;
		return true;

	},


	enableDisableEarlyBirdOffer: function(){
		$('#add_form_field_ebenddtchk').prop('checked', false); //.trigger('click');
		meetfuncs.allowDisallowEarlyBirdEndDate(false, null);
		$('#add_form_field_ebmaxcntchk').prop('checked', false); //.trigger('click');
		meetfuncs.allowDisallowEarlyBirdRegCnt(false,'');
		$('#add_form_field_ebtktprice').val('').prop('disabled', !$('#add_form_field_ebtktpricechk').is(':checked')).toggleClass('non-editable', !$('#add_form_field_ebtktpricechk').is(':checked')).get(0).focus();
	},

	allowDisallowEarlyBirdEndDate: function(status, dt=null){
		$('#add_form_field_ebenddt_picker').datepicker('setDate', dt);
		$('#add_form_field_ebenddt_picker').toggleClass('non-editable', !status).datepicker( "option", "disabled", !status);
		meetfuncs.setUnsetEarlyBirdRulesText();
	},

	allowDisallowEarlyBirdRegCnt: function(status, cnt=''){
		$('#add_form_field_ebmaxcnt').val(cnt).toggleClass('non-editable', !status);
		if(status)
			document.getElementById('add_form_field_ebmaxcnt').focus();
		meetfuncs.setUnsetEarlyBirdRulesText();
	},

	setUnsetEarlyBirdRulesText: function(){
		// let text= `Offer applies to the first 100 registrations done within 1st January 2025.`;
		let reg_cnt_text = dt_text = msg_text = msg_text1 = '';
		let msg_text2 = `persons who have registered`;
		if($('#add_form_field_ebenddtchk').is(':checked')){
			dt_text = $('#add_form_field_ebenddt_picker').datepicker("getDate")?.toLocaleDateString() || '';
			if(dt_text!='')
				dt_text = ` till ${dt_text}`;
		}

		if($('#add_form_field_ebmaxcntchk').is(':checked')){
			reg_cnt_text = $.trim($('#add_form_field_ebmaxcnt').val() || '');
			let reg_cnt = reg_cnt_text!==''?parseInt(reg_cnt_text, 10):'';
			if(reg_cnt_text!='' && reg_cnt>0){
				reg_cnt_text = ` first ${reg_cnt_text}`;
				msg_text1 = `A registration as a whole will qualify for the early bird offer if at least one person qualifies for the offer as per the set rules.`;
				if(parseInt(reg_cnt, 10)==1)
					msg_text2 = `person who has registered`;
			}else if(reg_cnt_text==='0'){
				reg_cnt_text = dt_text = '';
				msg_text = 'The Early Bird offer rules are set so as not to apply on any registration.';
			}

		}

		if(dt_text!='' || reg_cnt_text!='')
			msg_text= `The early bird offer will apply to the${reg_cnt_text} ${msg_text2} for the event${dt_text}. ${msg_text1}`;	
		$('#eb_applicability_text').find('span').text(msg_text).end().toggleClass('d-none', msg_text=='');
		
	},

	setheaderBarText:function(text){
		$("#header-bar-text").find(":first-child").html(text);
		// $('#panel-heading-text').text("Add user");

	},
  // PDF Download Function
	downloadAgenda: function(meetingId) {
		if (!meetingId) {
			alert('Meeting ID is required to download agenda.');
			return;
		}
		
		// Show loading message
		const originalText = 'Generating PDF...';
		
		// Create download URL
		const downloadUrl = `meetings.php?mode=downloadagenda&recid=${encodeURIComponent(meetingId)}`;

		document.getElementById("download-agenda-btn").href=downloadUrl;

		return true;
		
		// Use window.open for better compatibility
		const newWindow = window.open(downloadUrl, '_blank');
		
		// Fallback: if popup blocked, try direct navigation
		setTimeout(() => {
			if (!newWindow || newWindow.closed) {
				window.location.href = downloadUrl;
			}
		}, 100);
	},
	
	onHashChange:function(e){
		var hash=location.hash.replace(/^#/,'');
		// alert(hash);
		if(meetfuncs.curr_page_hash!=meetfuncs.prev_page_hash){
			meetfuncs.prev_page_hash=meetfuncs.curr_page_hash;
		}
		meetfuncs.curr_page_hash=hash;

		var hash_params={mode:''};
		if(hash!=''){
			var hash_params_temp=hash.split('&');
			var hash_params_count= hash_params_temp.length;
			for(var i=0; i<hash_params_count; i++){
				var temp=hash_params_temp[i].split('=');
				hash_params[temp[0]]=decodeURIComponent(temp[1]);
			}
		}



		switch(hash_params.mode.toLowerCase()){
			case 'addrec':
								$('.alert-success, .alert-danger').addClass('d-none');
								$('#msgFrm').removeClass('d-none');
								meetfuncs.openAddUserForm();
								break;

			case 'edit':
							$('.alert-success, .alert-danger').addClass('d-none');
							$('#msgFrm').removeClass('d-none');
							if(hash_params.hasOwnProperty('recid') && hash_params.recid!=''){
								meetfuncs.openRecordForEditing(hash_params.recid);

							}else{
								location.hash=meetfuncs.prev_page_hash;
							}
							break;

			case 'deleterec':
							$('.alert-success, .alert-danger').addClass('d-none');
							$('#msgFrm').removeClass('d-none');
							if(hash_params.hasOwnProperty('recid') && hash_params.recid!=''){
								meetfuncs.recordForDelete(hash_params.recid);

							}else{
								location.hash=meetfuncs.prev_page_hash;
							}
							break;				

			case 'printagenda':
				      $('.alert-success, .alert-danger').addClass('d-none');
							$('#msgFrm').removeClass('d-none');
							if(hash_params.hasOwnProperty('recid') && hash_params.recid!=''){
								meetfuncs.agendaForPrint(hash_params.recid);

							}else{
								location.hash=meetfuncs.prev_page_hash;
							}
							break;

			default:
					$('.alert-success, .alert-danger').addClass('d-none');
					$('#msgFrm').removeClass('d-none');
					var params={mode:'getList',pno:1, searchdata:"[]", sortdata:JSON.stringify(meetfuncs.sortparams), listformat:'html'};

					if(hash_params.hasOwnProperty('pno')){
						params['pno']=hash_params.pno
					}else{
						params['pno']=1;
					}

					if(hash_params.hasOwnProperty('searchdata')){
						params['searchdata']=hash_params.searchdata;

					}
					if(hash_params.hasOwnProperty('sortdata')){
						params['sortdata']=hash_params.sortdata;

					}

					meetfuncs.searchparams=JSON.parse(params['searchdata']);
					meetfuncs.sortparams=JSON.parse(params['sortdata']);

					if(meetfuncs.sortparams.length==0){
						meetfuncs.sortparams.push(meetfuncs.default_sort);
						params['sortdata']=JSON.stringify(meetfuncs.sortparams);
					}

					if(meetfuncs.searchparams.length>0){
							$.each(meetfuncs.searchparams , function(idx,data) {
									//console.log(data);
									switch (data.searchon) {

									
										case 'active':
											$("#search-field_active").val(data.searchtext);
											break;	
									}

							});
							//$('#close_box').removeClass('d-none');
						$("#search_field").val(meetfuncs.searchparams[0]['searchon'] || '');
					}
					// params['searchdata']=encodeURIComponent(params['searchdata']);
					// params['sortdata']=encodeURIComponent(params['sortdata']);

					if(meetfuncs.searchparams.length>0){
						if(meetfuncs.searchparams[0]['searchon'] == 'status')
							$("#search_text").val(meetfuncs.searchparams[0]['searchtext'][0]=='1'?'Active':'Inactive');
						else
							$("#search_text").val(meetfuncs.searchparams[0]['searchtext'] || '');

						$("#search_field").val(meetfuncs.searchparams[0]['searchon'] || '');
						//$('#close_box').removeClass('d-none');

					}

					$("#common-processing-overlay").removeClass('d-none');

					//alert("135");

					common_js_funcs.callServer({cache:'no-cache',async:true,dataType:'json',type:'post', url:self.ajax_data_script,params:params,successResponseHandler:meetfuncs.showList,successResponseHandlerParams:{self:meetfuncs}});

					var show_srch_form = false;
					if (typeof(Storage) !== "undefined") {
						srch_frm_visible = localStorage.event_search_toggle;
					} else {
						srch_frm_visible = Cookies.get('event_search_toggle');
					}
					if(srch_frm_visible && srch_frm_visible == 'visible')
						show_srch_form = true;
					$('.toggle-search').toggleClass('search-form-visible', show_srch_form);
					$('#search_records').closest('.panel-search').toggleClass('d-none', !show_srch_form);
					var search_form_cont = $('#search_records').closest('.panel-search');
					if(search_form_cont.hasClass('d-none'))
						$('.toggle-search').prop('title','Open search panel');
					else{
						$('.toggle-search').prop('title','Close search panel');
						$("#search-field_fullname").focus();
					}

				

		}






	}

}

document.addEventListener('DOMContentLoaded', function () {
    const meetDate = document.getElementById('meet_date');
    const meetDateTo = document.getElementById('meet_date_to');
    const sessionMeetDate = document.getElementById('session_meet_date_0');

    function toggleSessionMeetDateField() {
        const date1 = meetDate.value;
        const date2 = meetDateTo.value;

        if (date1 && date2 && date1 === date2) {
            sessionMeetDate.style.display = 'none';
        } else {
            sessionMeetDate.style.display = '';
        }
    }

    meetDate.addEventListener('change', toggleSessionMeetDateField);
    meetDateTo.addEventListener('change', toggleSessionMeetDateField);

    // Trigger once on load
    toggleSessionMeetDateField();
});

document.addEventListener('DOMContentLoaded', function () {
    // Existing code...

    /*document.getElementById('print-agenda-btn').addEventListener('click', function () {
        const meetingId = document.getElementById('rec_id').value || '';
        if (meetingId) {
            location.hash = 'mode=printagenda&recid=' + encodeURIComponent(meetingId);
        } else {
            alert('Meeting ID not found.');
        }
    });*/
});

/////// KB





<tr id="record_row_<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" class="aborted-task responsive-task-cat">

    <?php $action_mode = 'edit'; ?>

    <!-- Action Column -->
    <td class="text-center">
        <div class="">
            <a href="meetings.php#mode=<?php echo $action_mode; ?>&recid=<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" class="btn btn-xs btn-success user-edit-action record-edit-button" data-in-mode="list-mode" data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" data-rel='tooltip' title="Edit details">
                <img src="images/edit-white.png" class="custom-button-small" alt="Edit">
            </a>
            <a href="meetings.php#mode=deleterec&recid=<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" class="btn btn-xs btn-danger user-edit-action record-edit-button" data-in-mode="list-mode" data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" data-rel='tooltip' title="Delete meeting">
                <img src="images/cross.png" class="custom-button-small" alt="Delete">
            </a>
        </div>
    </td>

    <!-- Date Column -->
    <td data-label class="pointer clickable-cell" data-in-mode="list-mode" data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" data-hash="<?php echo 'mode='.$action_mode.'&recid='.$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>">
       <!--  <?php echo date('d-M-Y', strtotime($this->body_template_data[$mode_index]['records'][$i_ul]['meet_date'])); ?> -->
       <?php 
        $startDate = $this->body_template_data[$mode_index]['records'][$i_ul]['meet_date'];
        $endDate = $this->body_template_data[$mode_index]['records'][$i_ul]['meet_date_to'];

        $formattedStart = date('d-M-Y', strtotime($startDate));
        $formattedEnd = date('d-M-Y', strtotime($endDate));

        if ($startDate === $endDate || empty($endDate)) {
            echo $formattedStart;
        } else {
            echo $formattedStart . ' to ' . $formattedEnd;
        }
      ?>
    </td>

    <!-- Time Column -->
    <td data-label class="pointer clickable-cell" data-in-mode="list-mode" data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" data-hash="<?php echo 'mode='.$action_mode.'&recid='.$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>">
        <?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['meet_time']); ?>
        
    </td>
     <!-- Meeting Title -->
     <td data-label class="pointer clickable-cell" data-in-mode="list-mode" data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" data-hash="<?php echo 'mode='.$action_mode.'&recid='.$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>">
        <?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['meet_title']); ?>
    </td>

    <!-- Venue Column -->
    <td data-label class="pointer clickable-cell" data-in-mode="list-mode" data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" data-hash="<?php echo 'mode='.$action_mode.'&recid='.$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>">
        <?php \eBizIndia\_esc($this->body_template_data[$mode_index]['records'][$i_ul]['venue']); ?>
    </td>

    <!-- Absentees Column -->
    <td data-label class="pointer clickable-cell text-center" data-in-mode="list-mode" data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" data-hash="<?php echo 'mode='.$action_mode.'&recid='.$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>">
        <?php 
        $absentees_count = isset($this->body_template_data[$mode_index]['records'][$i_ul]['absentees_count']) ? (int)$this->body_template_data[$mode_index]['records'][$i_ul]['absentees_count'] : 0;
        echo $absentees_count > 0 ? $absentees_count : '';
        ?>
    </td>

    <!-- Presenters Column -->
    <td data-label class="pointer clickable-cell text-center" data-in-mode="list-mode" data-recid="<?php echo $this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>" data-hash="<?php echo 'mode='.$action_mode.'&recid='.$this->body_template_data[$mode_index]['records'][$i_ul]['id']; ?>">
        <?php 
        $presenters_count = isset($this->body_template_data[$mode_index]['records'][$i_ul]['presenters_count']) ? (int)$this->body_template_data[$mode_index]['records'][$i_ul]['presenters_count'] : 0;
        echo $presenters_count > 0 ? $presenters_count : '';
        ?>
    </td>

</tr>

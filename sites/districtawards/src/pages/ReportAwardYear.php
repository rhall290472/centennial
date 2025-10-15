<?php
/*
!==============================================================================!
!\                                                                            /!
!\\                                                                          //!
! \##########################################################################/ !
!  #         This is Proprietary Software of Richard Hall                   #  !
!  ##########################################################################  !
!  ##########################################################################  !
!  #                                                                        #  !
!  #                                                                        #  !
!  #   Copyright 2017-2024 - Richard Hall                                   #  !
!  #                                                                        #  !
!  #   The information contained herein is the property of Richard          #  !
!  #   Hall, and shall not be copied, in whole or in part, or               #  !
!  #   disclosed to others in any manner without the express written        #  !
!  #   authorization of Richard Hall.                                       #  !
!  #                                                                        #  !
!  #                                                                        #  !
! /##########################################################################\ !
!//                                                                          \\!
!/                                                                            \!
!==============================================================================!
*/

load_class(BASE_PATH . '/src/Classes/CDistrictAwards.php');
$cDistrictAwards = cDistrictAwards::getInstance();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="description" content="ReportAwardYear.php">
  <style>
    .fixed_header th,
    .fixed_header td {
      padding: 8px;
      text-align: left;
    }

    .dataTables_wrapper {
      margin: 20px;
    }

    /* Style for DataTables button container */
    .dt-buttons {
      margin-bottom: 10px;
      text-align: center;
    }

    .dt-buttons .btn {
      margin-right: 5px;
    }
  </style>
</head>

<body>
  <?php
  if (isset($_POST['SubmitYear'])) {
    $year = $_POST['Year'];
    $cDistrictAwards->SetYear($year);
  }
  $cDistrictAwards->SelectYear();

  // Set PDF filename and header based on selected year
  $year = $cDistrictAwards->GetYear();
  $pdf_filename = $year == "" ? "Nominees_All_Years" : "Nominees_" . $year;
  $pdf_header = $year == "" ? "Centennial District Awards - All Years" : "Centennial District Awards - " . $year;
  ?>

  <center>
    <h4><?php
        $msg = $year == "" ? "All Years" : $cDistrictAwards->GetYear();
        echo "Nominees for " . $msg
        ?> </h4>
    <table id="nomineesTable" class="fixed_header table table-striped table-bordered" style="width:100%">
      <thead>
        <tr>
          <th style='width:150px'>Year</th>
          <th style='width:150px'>First Name</th>
          <th style='width:150px'>Preferred Name</th>
          <th style='width:150px'>Last Name</th>
          <th style='width:500px'>Award</th>
          <th style='width:50px'>Status</th>
          <th style='width:150px'>Member ID</th>
          <th style='width:500px'>Unit</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($year == "")
          $queryNominees = "SELECT * FROM `district_awards` WHERE (`IsDeleted` IS NULL || `IsDeleted` <>'1') ORDER BY `Award`";
        else
          $queryNominees = "SELECT * FROM `district_awards` WHERE `Year`='$year' AND (`IsDeleted` IS NULL || `IsDeleted` <>'1') ORDER BY `Award`, `Year` DESC";

        if (!$ResultNominees = $cDistrictAwards->doQuery($queryNominees)) {
          $msg = "Error: doQuery()";
          $cDistrictAwards->function_alert($msg);
        }

        while ($rowNominee = $ResultNominees->fetch_assoc()) {
          if ($rowNominee['NomineeIDX'] != -1) {
            $AwardName = $cDistrictAwards->GetAwardName($rowNominee['Award']);
            $Status = $cDistrictAwards->GetAwardStatus($rowNominee['Status']);
            echo "<tr><td style='width:150px'>" .
              $rowNominee["Year"] . "</td><td style='width:150px'>" .
              $rowNominee["FirstName"] . "</td><td style='width:150px'>" .
              $rowNominee["PName"] . "</td><td style='width:150px'>" .
              "<a href=index.php?page=edit-nominee&NomineeIDX=" . $rowNominee['NomineeIDX'] . ">" . $rowNominee['LastName'] . "</a></td><td style='width:500px'>" .
              $AwardName . "</td><td style='width:50px'>" .
              $Status . "</td><td style='width:150px'>" .
              $rowNominee['MemberID'] . "</td><td style='width:500px'>" .
              $rowNominee['Unit'] . "</td></tr>";
          }
        }
        ?>
      </tbody>
    </table>
  </center>

  <script>
    $(document).ready(function() {
      $('#nomineesTable').DataTable({
        dom: 'Bfrtip',
        paging: false, // Disable pagination to remove Previous/Next buttons
        buttons: [{
            extend: 'copy',
            className: 'btn btn-primary btn-sm d-print-none mt-2',
            text: 'Copy to Clipboard',
            exportOptions: {
              columns: ':visible'
            }
          },
          {
            extend: 'csv',
            className: 'btn btn-primary btn-sm d-print-none mt-2',
            text: 'Export CSV',
            exportOptions: {
              columns: ':visible'
            }
          },
          {
            extend: 'excel',
            className: 'btn btn-primary btn-sm d-print-none mt-2',
            text: 'Export Excel',
            exportOptions: {
              columns: ':visible'
            }
          },
          {
            extend: 'pdf',
            className: 'btn btn-primary btn-sm d-print-none mt-2',
            text: 'Export PDF',
            filename: '<?php echo $pdf_filename; ?>', // Custom PDF filename
            orientation: 'landscape', // Maintain landscape mode
            exportOptions: {
              columns: ':visible'
            },
            customize: function(doc) {
              // Add header to every page
              doc.header = {
                text: '<?php echo $pdf_header; ?>',
                alignment: 'center',
                margin: [0, 10, 0, 10],
                fontSize: 12
              };
              // Set page margins to maximize table width
              doc.pageMargins = [20, 30, 20, 20]; // Increased top margin to accommodate header
              // Adjust column widths to fit within one page (proportional to content)
              doc.content[1].table.widths = ['auto', 'auto', 'auto', 'auto', '*', 'auto', 'auto', '*'];
              // Set font sizes for better readability
              doc.styles.tableHeader.fontSize = 8;
              doc.styles.tableBodyOdd.fontSize = 7;
              doc.styles.tableBodyEven.fontSize = 7;
              // Ensure table fits page width
              doc.content[1].table.layout = {
                hLineWidth: function(i, node) {
                  return 0.5;
                },
                vLineWidth: function(i, node) {
                  return 0.5;
                },
                paddingLeft: function(i, node) {
                  return 2;
                },
                paddingRight: function(i, node) {
                  return 2;
                },
                paddingTop: function(i, node) {
                  return 2;
                },
                paddingBottom: function(i, node) {
                  return 2;
                }
              };
            }
          }
        ],
        order: [
          [0, 'desc']
        ], // Default sort by Year descending
        columnDefs: [{
            type: 'string',
            targets: '_all'
          } // Ensure proper string sorting
        ]
      });
    });
  </script>
</body>

</html>
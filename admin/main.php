<?php if(isset($_SESSION['branch_id'])){ ?>
    <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="py-4 mb-6">Page 1</h4>
      <p>Sample page.<br />For more layout options, refer <a href="https://demos.pixinvent.com/materialize-html-admin-template/documentation//layouts.html" target="_blank" class="fw-medium">Layout docs</a></p>
    </div>
<?php } ?>
<?php
// ... (โค้ดส่วนอื่นๆ เช่น session_start(), include, require) ...

// ดึงข้อมูลสาขาจากฐานข้อมูล

?>
<form action="<?php echo $editFormAction; ?>" method="post" id="mainForm">
  <input type="hidden" name="branch_id" id="selectedBranchId"> 
  </form>

<div class="modal fade" id="autoShowModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="autoShowModalLabel">เลือกสาขา</h5>
      </div>
      <div class="modal-body">
        <select class="form-select" id="branchDropdown">
          <?php
          if ($_SESSION['position_id']==1) {
              $sql_branch = "SELECT branch_id, branch_name FROM branch";
              $result_branch = $conn->query($sql_branch);
              while ($row_branch = $result_branch->fetch_object()) {
              echo "<option value='" . $row_branch->branch_id . "'>" . $row_branch->branch_name . "</option>";
            }
          }

          ?>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="selectBranch()">เลือก</button>
      </div>
    </div>
  </div>
</div>


<script>
  <?php 
    if(!isset($_SESSION['branch_id'])){?>
        // เมื่อหน้าเว็บโหลดเสร็จ
        window.onload = function() {
          var myModal = new bootstrap.Modal(document.getElementById('autoShowModal'));
          myModal.show();
        }
    <?php } ?>


function selectBranch() {
  var selectedBranchId = document.getElementById("branchDropdown").value;
  // เปลี่ยนเส้นทางไปยังหน้าอื่นพร้อมกับค่า branch_id
  window.location.href = 'index.php?branch_id=' + selectedBranchId; 
}


</script>


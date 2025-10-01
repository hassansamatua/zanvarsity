        </div><!-- End container-fluid -->
    </div><!-- End main-content -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Toggle sidebar on mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (sidebarToggle && sidebar && mainContent) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    sidebar.classList.toggle('d-none');
                    mainContent.classList.toggle('full-width');
                });
            }
            
            // Add active class to current nav item
            const currentPage = window.location.pathname.split('/').pop() || 'dashboard.php';
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPage) {
                    link.classList.add('active');
                    // Expand parent dropdown if exists
                    const parentCollapse = link.closest('.collapse');
                    if (parentCollapse) {
                        parentCollapse.classList.add('show');
                        const parentLink = document.querySelector('[href="#' + parentCollapse.id + '"]');
                        if (parentLink) {
                            parentLink.classList.add('active');
                            parentLink.setAttribute('aria-expanded', 'true');
                        }
                    }
                }
            });
            
            // Initialize DataTables with common options
            if (typeof $.fn.DataTable === 'function') {
                $('.datatable').DataTable({
                    responsive: true,
                    stateSave: true,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "No entries found",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                         '<"row"<"col-sm-12"tr>>' +
                         '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    lengthMenu: [10, 25, 50, 100],
                    pageLength: 10
                });
            }
            
            // Initialize select2 if available
            if (typeof $().select2 === 'function') {
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });
            }
            
            // Initialize datepickers
            if (typeof $().datepicker === 'function') {
                $('.datepicker').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayHighlight: true
                });
            }
            
            // Initialize timepickers
            if (typeof $().timepicker === 'function') {
                $('.timepicker').timepicker({
                    showMeridian: false,
                    minuteStep: 15
                });
            }
            
            // Initialize file input
            if (typeof $().fileinput === 'function') {
                $('.file-input').fileinput({
                    showUpload: false,
                    showCaption: false,
                    browseClass: 'btn btn-primary',
                    removeClass: 'btn btn-danger',
                    mainClass: 'input-group',
                    previewFileType: 'any',
                    allowedFileExtensions: ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'],
                    maxFileSize: 5120, // 5MB
                    maxFilesCount: 5
                });
            }
            
            // Form validation
            if (typeof $().validate === 'function') {
                $('.needs-validation').validate({
                    errorElement: 'div',
                    errorClass: 'invalid-feedback',
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('is-invalid').removeClass(validClass);
                        $(element).closest('.form-group').addClass('has-error');
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('is-invalid').addClass(validClass);
                        $(element).closest('.form-group').removeClass('has-error');
                    },
                    errorPlacement: function(error, element) {
                        if (element.parent('.input-group').length) {
                            error.insertAfter(element.parent());
                        } else if (element.hasClass('select2-hidden-accessible')) {
                            error.insertAfter(element.next('span.select2'));
                        } else {
                            error.insertAfter(element);
                        }
                    }
                });
            }
            
            // Confirm before delete
            $('.confirm-delete').on('click', function(e) {
                e.preventDefault();
                const confirmMessage = $(this).data('confirm') || 'Are you sure you want to delete this item?';
                if (confirm(confirmMessage)) {
                    const form = $(this).closest('form');
                    if (form.length) {
                        form.submit();
                    } else {
                        window.location.href = $(this).attr('href');
                    }
                }
            });
            
            // Auto-hide alerts after 5 seconds
            window.setTimeout(function() {
                $('.alert:not(.alert-permanent)').fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove();
                });
            }, 5000);
        });
    </script>
    
    <!-- Custom scripts for specific pages -->
    <?php if (isset($custom_scripts)) echo $custom_scripts; ?>
    
    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editUserForm" method="post">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_role" class="form-label">Role</label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="student">Student</option>
                                <option value="instructor">Instructor</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete User Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteUserForm" method="post">
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user_id" id="delete_user_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    
                    <div class="modal-body">
                        <p>Are you sure you want to delete user "<span id="delete_user_name"></span>"? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<!-- Модальное окно для добавления пользователя -->

<div id="modal_add_user" class="modal">
    <div id="modal_container">
        <div id="modal_content" class="modal_content ">
            <div class="modal_head">
                <i class="icon_close" onclick="modalAdd.modal_hide()"></i>
            </div>
            <div class="modal_body">
                <form id="registrationForm">
                    <div class="input_group_modal">
                        <label for="firstName">
                            First Name: <span class="required">*</span>
                        </label>
                        <input name="firstName" id="firstName" type="text" placeholder="Enter first name"
                            autocomplete="given-name">
                        <div class="error-message" id="firstNameError">Пожалуйста, введите имя</div>
                    </div>

                    <div class="input_group_modal">
                        <label for="lastName">
                            Last Name: <span class="required">*</span>
                        </label>
                        <input type="text" id="lastName" placeholder="Enter last name" autocomplete="family-name">
                        <div class="error-message" id="lastNameError">Пожалуйста, введите фамилию</div>
                    </div>

                    <div class="input_group_modal">
                        <label for="email">
                            Email: <span class="required">*</span>
                        </label>
                        <input type="email" id="email" name="email" placeholder="Enter email" autocomplete="email">

                        <p class="error-message" id="emailError">Пожалуйста, введите корректный email</p>
                    </div>

                    <div class="input_group_modal">
                        <label for="phone">
                            Phone: <span class="required">*</span>
                        </label>
                        <input type="tel" id="phone" name="phone" placeholder="phone" autocomplete="tel">
                        <div class="error-message" id="phoneError">Пожалуйста, введите номер телефона</div>
                    </div>

                    <p class="input_group_modal">
                        <label>Plots (IDs, comma-separated):
                            <input type="text" name="plots" id="plots" placeholder="e.g., 1,2,3"></label>
                    </p>
                    <p class="input_group_modal">
                        <label>Code (4 digits):
                            <input type="text" name="phoneCode" id="phoneCode" placeholder="Enter 4-digit code"
                                maxlength="4" pattern="\d*"></label>
                    </p>
                    <div class="modal_controls">

                        <div class="btn_modal">

                            <button class="btn_modal" type="submit" id="submitBtn" onclick="modalAdd.user_add()">
                                Save
                            </button>
                        </div>
                        <div>
                            <button class="btn_modal light" onclick="modalAdd.modal_hide()">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




<!-- Модальное окно для редактирования пользователя -->

<div id="modal_edit_user" class="modal">
    <div class="modal_container">
        <div id="modal_content" class="modal_content ">
            <div class="modal_head">
                <i class="icon_close" onclick="modalEdit.modal_hide()"></i>
            </div>
            <div class="modal_body">
                <form action="" method="post" id="edit_user_form">

                    <div class="input_group_modal">
                        <label for='first_name'>First Name</label>
                        <input type="text" name="first_name" id="edit_first_name" placeholder="Enter first name"
                            required>
                    </div>

                    <div class="input_group_modal">
                        <label>Last Name</label>
                        <input type="text" id="edit_last_name" placeholder="Enter last name" required>
                    </div>

                    <div class="input_group_modal">
                        <label>Phone</label>

                        <input type="tel" id="edit_phone" placeholder="Enter phone number " required>
                    </div>

                    <div class="input_group_modal">
                        <label>Email</label>
                        <input type="email" id="edit_email" placeholder="Enter email" required>
                    </div>

                    <div class="input_group_modal">
                        <label>Plot ID</label>
                        <input type="number" id="edit_plot_id" placeholder="Enter plot ID" required>
                    </div>

                    <div class="input_group_modal">
                        <label>Last Login</label>
                        <input type="text" id="edit_last_login" readonly>
                    </div>
                    <div class="modal_controls">

                        <div>
                            <button class="btn_modal" onclick="modalEdit.update_user(event)">Save</button>
                        </div>
                        <div>
                            <button class="btn_modal light" onclick="modalEdit.modal_hide()">Cancel</button>
                        </div>
                    </div>
                </form>


            </div>
        </div>
    </div>
</div>



<!-- Удаление пользователя -->
<div id="modal_delete_user" class="modal">
    <div id="modal_container">
        <div id="modal_content" class="modal_content">
            <div class="modal_head">
                <i class="icon_close" onclick="modalEdit.modal_hide()"></i>
            </div>
            <div class="modal_body">
                <input type="hidden" id="edit_user_id">
                <!-- Поля формы -->
                <div class="input_group_modal">
                    <label>First Name</label>
                    <input type="text" id="edit_first_name" placeholder="Enter first name">
                </div>
                <!-- Остальные поля -->
                <div class="modal_controls">
                    <div>
                        <button class="btn_modal" onclick="modalEdit.user_edit()">Save</button>
                    </div>
                    <div>
                        <button class="btn_modal light" onclick="modalEdit.modal_hide()">Cancel</button>
                    </div>
                    <div>
                        <button class="btn_modal danger" onclick="modalEdit.user_delete()">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
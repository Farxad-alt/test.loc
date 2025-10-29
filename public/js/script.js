
// Функция для поиска plots
const search = {
  search_do: function (type) {
    const query = document.getElementById('search').value.trim();
    const tableBody = document.querySelector('table tbody');

    if (!tableBody) {
      console.error('Элемент <tbody> не найден!');
      return;
    }

    tableBody.innerHTML = '<tr><td colspan="7">Загрузка...</td></tr>';

    fetch(`/api/search_${type}.php?query=${encodeURIComponent(query)}&page=1`)
      .then(response => {
        if (!response.ok) {
          throw new Error(`Ошибка HTTP: ${response.status}`);
        }
        return response.text(); // Сначала читаем как текст, чтобы проверить содержимое
      })
      .then(text => {
        try {
          const data = JSON.parse(text);

          if (!data.success) {
            tableBody.innerHTML = `<tr><td colspan="7">Ошибка сервера: ${data.error}</td></tr>`;
            return;
          }

          if (data.data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="7">Ничего не найдено</td></tr>';
            return;
          }

          tableBody.innerHTML = ''; // Очищаем загрузку

          data.data.forEach(item => {
            const row = document.createElement('tr');

            if (type === 'plots') {
              row.innerHTML = `
                <td>${item.number}</td>
                <td>${item.size}</td>
                <td>${item.status}</td>
                <td>${item.billing}</td>
                <td>${item.price} AED</td>
                <td class="right_column">
                  <i class="icon icon_ellipsis" onclick="Search.menu_popup_toggle(this, event);">
                    <span class="menu_popup">
                      <div>Edit</div>
                    </span>
                  </i>
                </td>
              `;
            } else if (type === 'users') {
              row.innerHTML = `
                <td>${item.user_id}</td>
                <td>${item.first_name}</td>
                <td>${item.last_name}</td>
                <td>${item.phone}</td>
                <td>${item.email}</td>
                <td>${item.last_login ? new Date(item.last_login).toLocaleString() : '-'}</td>
                <td class="right_column">
                  <i class="icon icon_ellipsis" onclick="modalEdit.menu_popup_toggle(this, event);">
                    <div class="menu_popup">
                      <div onclick="modalEdit.edit_user(${item.user_id})">Edit</div>
                      <div onclick="modalEdit.delete_user(${item.user_id})">Delete</div>
                    </div>
                  </i>
                </td>
              `;
            }

            tableBody.appendChild(row);
          });

        } catch (e) {
          console.error("Ошибка парсинга JSON:", e);
          console.log("Полученный ответ:", text);
          tableBody.innerHTML = '<tr><td colspan="7">Ошибка загрузки данных</td></tr>';
        }
      })
      .catch(error => {
        console.error('Ошибка поиска:', error);
        tableBody.innerHTML = '<tr><td colspan="7">Ошибка загрузки данных</td></tr>';
      });
  }
};





// Редактирование пользователя
const modalEdit = {
  menu_popup_toggle: function (element, event) {
    event.stopPropagation();
    const popup = element.querySelector('.menu_popup');
    if (!popup) return;

    popup.classList.toggle('active');

    document.addEventListener('click', function closePopup(e) {
      if (!element.contains(e.target)) {
        popup.classList.remove('active');
        document.removeEventListener('click', closePopup);
      }
    });
  },

  edit_user: function (userId) {
    fetch('/api/get_user.php?user_id=' + userId)
      .then(response => response.json())
      .then(data => {
       

        if (data.success && data.user) {
          const user = data.user;


          // Очистка полей перед заполнением
          document.getElementById('edit_user_id').value = '';
          document.getElementById('edit_first_name').value = '';
          document.getElementById('edit_last_name').value = '';
          document.getElementById('edit_phone').value = '';
          document.getElementById('edit_email').value = '';
          document.getElementById('edit_plot_id').value = '';
          document.getElementById('edit_last_login').value = '';

          // Заполнение полей
          document.getElementById('edit_user_id').value = user.user_id;
          document.getElementById('edit_first_name').value = user.first_name || '';
          document.getElementById('edit_last_name').value = user.last_name || '';
          document.getElementById('edit_phone').value = (user.phone || '').toString().replace('+7', '');
          document.getElementById('edit_email').value = user.email || '';
          document.getElementById('edit_plot_id').value = user.plot_id || '';
          document.getElementById('edit_last_login').value = user.last_login || '';

          modalEdit.modal_show();
        } else {
          alert('Ошибка: ' + (data.error || "Неизвестная ошибка"));
        }
      })
      // .catch(error => {
      //   console.error('Ошибка:', error);
      //   alert('Не удалось загрузить данные пользователя.');
      // });
  },

    update_user: function () {
      const userId = document.getElementById('edit_user_id').value;
      const firstName = document.getElementById('edit_first_name').value.trim();
      const lastName = document.getElementById('edit_last_name').value.trim();
      const phoneInput = document.getElementById('edit_phone').value.trim();
      const email = document.getElementById('edit_email').value.trim();
      const plotId = document.getElementById('edit_plot_id').value.trim();

      // Проверка наличия user_id
      if (!userId) {
        alert("User ID is missing!");
        return;
      }

      // Валидация телефона
      // let cleanedPhone = phoneInput.replace(/\D/g, '');
      // const isValidPhone = /^(\+7|8)?[\d]{10}$/.test(cleanedPhone);

      // if (!isValidPhone) {
      //   document.getElementById('edit_phone').classList.add('invalid');
      //   alert("Неверный формат телефона!");
      //   return;
      // }

      document.getElementById('edit_phone').classList.remove('invalid');
      document.getElementById('edit_phone').classList.add('valid');

      // Проверка обязательных полей
      // if (!firstName || !lastName || !phoneInput || !email) {
      //   alert("Все обязательные поля должны быть заполнены!");
      //   return;
      // }

      // Отправка данных на сервер
      fetch('/api/edit_user.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          user_id: userId,
          first_name: firstName,
          last_name: lastName,
          phone: phoneInput,
          email: email,
          plot_id: plotId ? parseInt(plotId) : null
        })
      })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          if (data.success) {
            alert("✅ Пользователь успешно обновлён!");
            modalEdit.modal_hide();
            search.search_do('users'); // Обновляем таблицу
          } else {
            alert("Ошибка: " + (data.error || "Неизвестная ошибка"));
          }
        })
        // .catch(error => {
        //   console.error("Ошибка:", error);
        //   alert("Не удалось обновить пользователя: " + error.message);
        // });

},


  modal_show: function () {
    document.getElementById('modal_edit_user').style.display = 'block';
  },

  modal_hide: function () {
    document.getElementById('modal_edit_user').style.display = 'none';
  },
  delete_user: function (userId) {
    if (!confirm("Вы уверены, что хотите удалить этого пользователя?")) {
      return;
    }

    fetch(`/api/delete_user.php?id=${userId}`, {
      method: 'DELETE'
    })
      .then(response => {
        if (!response.ok) throw new Error('Network error');
        return response.json();
      })
      .then(data => {
        if (data.success) {
          alert("✅ Пользователь успешно удален!");
          modalEdit.modal_hide();
          search.search_do('users');// Обновить таблицу
        } else {
          alert("Error: " + (data.error || "Unknown error"));
        }
      })
      .catch(error => {
        console.error("Ошибка:", error);
        alert("Не удалось удалить пользователя.");
      });
  },
  save_user: function (event) {
   
    if (event) event.preventDefault();

    const userId = document.getElementById('edit_user_id').value;
    const firstName = document.getElementById('edit_first_name').value.trim();
    const lastName = document.getElementById('edit_last_name').value.trim();
    const phoneInput = document.getElementById('edit_phone').value.trim();
    const email = document.getElementById('edit_email').value.trim();
    const plotId = document.getElementById('edit_plot_id').value.trim();

    // Очистка сообщений об ошибках
    document.getElementById('edit_error_message').style.display = 'none';
    document.getElementById('edit_error_message').textContent = '';

    // Валидация телефона
    // let cleanedPhone = phoneInput.replace(/\D/g, '');
    // const isValidPhone = /^(\+7|8)?[\d]{11}$/.test(cleanedPhone);

    // Сброс стилей
    document.getElementById('edit_phone').classList.remove('valid', 'invalid');

     if (!isValidPhone) {
        document.getElementById('edit_phone').classList.add('invalid');
        document.getElementById('edit_error_message').textContent = 'Неверный формат телефона!';
        document.getElementById('edit_error_message').style.display = 'block';
        return;
    }
    // Проверка обязательных полей
    if (!firstName || !lastName || !email || !phoneInput || !plotId) {
        document.getElementById('edit_error_message').textContent = 'Заполните все обязательные поля!';
        document.getElementById('edit_error_message').style.display = 'block';
        return;
    }

    // Отправка данных на сервер
    fetch('/api/edit_user.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            user_id: userId,
            first_name: firstName,
            last_name: lastName,
            email: email,
            phone: phoneInput.startsWith('+7') ? phoneInput : '+7' + phoneInput,
            plot_id: parseInt(plotId)
        })
    })
        .then(response => {
            return response.json().then(data => {
                if (!response.ok) {
                    throw new Error(data.error || 'Network error');
                }
                return data;
            });
        })
      
        .then(data => {
            if (data.success) {
                
            alert("✅ Пользователь успешно обновлён!");
                modalEdit.modal_hide();
                search.search_do('users'); // Обновление таблицы
                
                document.getElementById('edit_user_id').value = '';
                document.getElementById('edit_first_name').value = '';
                document.getElementById('edit_last_name').value = '';
                document.getElementById('edit_email').value = '';
                document.getElementById('edit_phone').value = '';
                document.getElementById('edit_plot_id').value = '';
            } else {
                document.getElementById('edit_error_message').textContent = data.error || 'Неизвестная ошибка';
                document.getElementById('edit_error_message').style.display = 'block';
            }
        })
        .catch(error => {
            console.error("Ошибка:", error);
            document.getElementById('edit_error_message').textContent = 'Не удалось обновить пользователя.';
            document.getElementById('edit_error_message').style.display = 'block';
        });
}

};


// Добавление участка
const modal_plots = {
  modal_show: function () {
    document.getElementById('modal_add_plot').style.display = 'flex';
  },
  modal_hide: function () {
    document.getElementById('modal_add_plot').style.display = 'none';
  },
  plot_add: function () {
    const status = document.getElementById('status').value;
    const billing = document.getElementById('billing').value;
    const number = document.getElementById('number').value.trim();
    const size = document.getElementById('size').value.trim();
    const price = document.getElementById('price').value.trim();

    if (!number || !size || !price) {
      alert('Please fill all required fields');
      return;
    }

    fetch('/api/add_plot.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        status,
        billing,
        number,
        size,
        price
      })
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Plot added successfully');
          modal_plots.modal_hide();
          location.reload(); // Перезагрузка страницы
        } else {
          alert('Error: ' + data.error);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Server error');
      });
  }
};


// Функция выхода
(function () {
  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', function (e) {
      e.preventDefault();

      fetch('/logout.php', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then(response => {
          if (response.ok) {
            window.location.href = '/index.php';
          } else {
            alert('Ошибка при выходе');
          }
        })
        .catch(error => {
          console.error('Ошибка:', error);
          alert('Не удалось выйти');
        });
    });
  }
})();


// ✅ Автоматически устанавливаем фокус на поле ввода кода
if (window.location.search.includes("step=code")) {
  document.addEventListener("DOMContentLoaded", function () {
    const codeInput = document.querySelector("#codeForm input[name='code']");
    if (codeInput) {
      codeInput.focus();
    }
  });
}


// Модальное окно добавления пользователя
const modalAdd = {
    modal_show: function () {
        const modalElement = document.getElementById('modal_add_user');
        if (!modalElement) {
            console.error("Модальное окно не найдено!");
            return;
        }
        modalElement.classList.add('active');
    },
    modal_hide: function () {
        const modalElement = document.getElementById('modal_add_user');
        if (modalElement) {
            modalElement.classList.remove('active');
        }
    },
  user_add: function () {
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const phoneInput = document.getElementById('phone').value.trim();
    const email = document.getElementById('email').value.trim();
    const plotsInput = document.getElementById('plots').value.trim();
    const code = document.getElementById('phoneCode').value.trim();

    // Валидация обязательных полей
    if (!firstName || !lastName || !phoneInput || !email || !code) {
      alert("Все обязательные поля должны быть заполнены!");
      return;
    }

    // Проверка кода
    if (code.length !== 4 || !/^\d+$/.test(code)) {
      alert("Код должен состоять из 4 цифр!");
      return;
    }

    // Обработка участков
    const plotIds = plotsInput ? plotsInput.split(',').map(id => parseInt(id.trim())) : [];

    console.log("🚀 Начало отправки данных пользователя...");

    fetch('/api/add_user.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        first_name: firstName,
        last_name: lastName,
        phone: phoneInput,
        email: email,
        plot_ids: plotIds,
        phone_code: code
      })
    })
      .then(response => {
        console.log("✅ Ответ от сервера:", response.status);
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        return response.json();
      })
      .then(data => {
        console.log("✅ Данные пользователя:", data);
        if (data.success) {
          alert("✅ Пользователь успешно добавлен!");
          modalAdd.modal_hide();
          search.search_do('users'); // Обновляем таблицу
        } else {
          alert("❌ Ошибка: " + (data.error || "Неизвестная ошибка"));
        }
      })
      .catch(error => {
        console.error("❌ Ошибка:", error);
        alert("❌ Произошла ошибка при регистрации. Пожалуйста, попробуйте снова.");
      });
  }

};



// Валидация 
  const form = document.getElementById('registrationForm');
  const submitBtn = document.getElementById('submitBtn');
  const successMessage = document.getElementById('successMessage');

  const fields = {
    firstName: {
      element: document.getElementById('firstName'),
      error: document.getElementById('firstNameError'),
      validate: (value) => value.trim().length > 0
    },
    lastName: {
      element: document.getElementById('lastName'),
      error: document.getElementById('lastNameError'),
      validate: (value) => value.trim().length > 0
    },
    email: {
      element: document.getElementById('email'),
      error: document.getElementById('emailError'),
      validate: (value) => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(value.trim());
      }
    },
    phone: {
      element: document.getElementById('phone'),
      error: document.getElementById('phoneError'),
      validate: (value) => {
        const cleanPhone = value.replace(/\D/g, '');
        return cleanPhone.length >= 10;
      }
    },
 phoneCode: {
    element: document.getElementById('phoneCode'),
    error: document.getElementById('phoneError'),

    validate: function () {
        const value = this.element.value.trim();
        
        // Проверка длины (4 цифры)
        if (value.length !== 4) {
            this.showError('Phone code must be 4 digits');
            return false;
        }

        // Проверка, что все символы — цифры
        if (!/^\d+$/.test(value)) {
            this.showError('Phone code must contain only digits');
            return false;
        }

        this.hideError();
        return true;
    },

    showError: function (message) {
        this.error.textContent = message;
        this.error.style.display = 'block';
        this.element.classList.add('invalid');
    },

    hideError: function () {
        this.error.textContent = '';
        this.error.style.display = 'none';
        this.element.classList.remove('invalid');
    }
},

  };

  function validateField(fieldName) {
    const field = fields[fieldName];
    const value = field.element.value;
    const isValid = field.validate(value);

    if (isValid) {
      field.element.classList.remove('error');
      field.element.classList.add('success');
      field.error.classList.remove('show');
    } else {
      field.element.classList.remove('success');
      field.element.classList.add('error');
      field.error.classList.add('show');
    }

    return isValid;
  }

  function clearFieldStatus(fieldName) {
    const field = fields[fieldName];
    field.element.classList.remove('error', 'success');
    field.error.classList.remove('show');
  }

  fields.email.element.addEventListener('input', (e) => {
    e.target.value = e.target.value.toLowerCase();
    if (e.target.classList.contains('error')) {
      clearFieldStatus('email');
    }
  });



  fields.firstName.element.addEventListener('blur', () => {
    if (fields.firstName.element.value.trim()) {
      validateField('firstName');
    }
  });

  fields.firstName.element.addEventListener('input', () => {
    if (fields.firstName.element.classList.contains('error')) {
      clearFieldStatus('firstName');
    }
  });

  fields.lastName.element.addEventListener('blur', () => {
    if (fields.lastName.element.value.trim()) {
      validateField('lastName');
    }
  });

  fields.lastName.element.addEventListener('input', () => {
    if (fields.lastName.element.classList.contains('error')) {
      clearFieldStatus('lastName');
    }
  });

  fields.email.element.addEventListener('blur', () => {
    if (fields.email.element.value.trim()) {
      validateField('email');
    }
  });

  fields.phone.element.addEventListener('blur', () => {
    if (fields.phone.element.value.trim()) {
      validateField('phone');
    }
  });
  fields.phoneCode.element.addEventListener('blur', () => {
    if (fields.phoneCode.element.value.trim()) {
      validateField('phoneCode');
    }
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();



    let isFormValid = true;
    Object.keys(fields).forEach(fieldName => {
      if (!validateField(fieldName)) {
        isFormValid = false;
      }
    });

    if (!isFormValid) {
      return;
    }

    
   

    // try {
    //   const firstName = fields.firstName.element.value.trim();
    //   const lastName = fields.lastName.element.value.trim();
    //   const email = fields.email.element.value.trim().toLowerCase();
    //   // const phone = fields.phone.element.value.replace(/\D/g, '');
    //   const  phoneCode = fields.phoneCode.element.value.replace(/\D/g, '');

    //   const { data, error } = await supabase
    //     .from('users')
    //     .insert([
    //       {
    //         first_name: firstName,
    //         last_name: lastName,
    //         email: email,
    //         // phone: phone,
    //         phoneCode: phoneCode
    //       }
    //     ])
    //     .select();

    //   if (error) {
    //     if (error.code === '23505') {
    //       alert('Пользователь с таким email уже существует');
    //     } else {
    //       throw error;
    //     }
    //     return;
    //   }

    //   successMessage.classList.add('show');
    //   form.reset();
    //   Object.keys(fields).forEach(fieldName => {
    //     clearFieldStatus(fieldName);
    //   });

    //   setTimeout(() => {
    //     successMessage.classList.remove('show');
    //   }, 5000);

  
    // } finally {
    //   submitBtn.disabled = false;
    //   submitBtn.innerHTML = 'Зарегистрироваться';
    // }
  });

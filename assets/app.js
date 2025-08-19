/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import $ from 'jquery';
import * as bootstrap from 'bootstrap';

global.$ = global.jQuery = $;

$(function () {
    const modalEl = document.getElementById('editUserModal');
    const modal = new bootstrap.Modal(modalEl);

    $('.edit-user-btn').on('click', function () {
        $('#editUserId').val($(this).data('id'));
        $('#editUserName').val($(this).data('name'));
        $('#editUserEmail').val($(this).data('email'));
        $('#editUserStatus').val($(this).data('status'));
        modal.show();
    });

    $('#editUserForm').on('submit', function (e) {
        e.preventDefault();

        const id = $('#editUserId').val();
        const data = {
            name: $('#editUserName').val(),
            email: $('#editUserEmail').val(),
            status: $('#editUserStatus').val()
        };

        $.ajax({
            url: '/user/' + id + '/edit',
            type: "POST",
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function () {
                modal.hide();

                const row = $('button[data-id="' + id + '"]').closest('tr');
                row.find('td:eq(1)').text(data.name);
                row.find('td:eq(2)').text(data.email); 
                row.find('td:eq(4)').text(data.status);

                const button = row.find('.edit-user-btn');
                button.data('name', data.name);
                button.data('email', data.email);
                button.data('status', data.status);            
            },
            error: function (xhr) {
                alert('Błąd: ' + (xhr.responseJSON?.error ?? 'Nieznany błąd'));
            }
        });
    });
});

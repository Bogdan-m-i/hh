// import bootstrap from 'bootstrap';

window.addEventListener('DOMContentLoaded', function () {

    const form = document.querySelector('.needs-validation')
    const inputMsg = form.querySelector('[name="msg"')

    form.addEventListener('submit', function (event) {
        event.preventDefault()
        if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
        } else {
            let formData = new FormData(form)
            sendComment(formData)
        }

        form.classList.add('was-validated')
    })

    function sendComment(formData) {
        fetch('/backend/addComment', {
            method: 'POST',
            body: formData,
        }).then((res) => {
            return res.json()
        }).then((res) => {
            if (res) {
                console.log(res)
                render([res])
                inputMsg.value = '';
                form.classList.remove('was-validated')
            } else {
                console.log('Ошибка при добавлении сообщения в БД')
            }
        })
    }

    function getComments() {
        fetch('/backend/getComments', {
            method: 'POST',
        }).then((res) => {
            return res.json()
        }).then((res) => {
            if (res) {
                console.log(res)
                render(res)
            } else {
                console.log('Ошибка при получении сообщений')
            }
        })
    }
    getComments()

    function render(data = []) {
        const container = document.querySelector('#comments')

        container.insertAdjacentHTML('afterbegin', data.map((el) => {
            return getTemplate(el.name, el.email, el.msg)
        }).join(' '))
    }

    function getTemplate(name, email, msg) {
        return `
            <div class="col card-wrapper">
                <div class="card mx-0 mx-lg-3 mb-5 text-center">
                    <div class="card-title fw-bold mb-4 py-3">${name}</div>
                    <div class="card-body">
                        <p class="card-text card-email fw-bold mb-4">${email}</p>
                        <p class="card-text card-msg mb-5">${msg}</p>
                    </div>
                </div>
            </div>
        `
    }

});
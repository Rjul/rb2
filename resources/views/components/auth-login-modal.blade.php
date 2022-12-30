<button type="button" style="z-index: 2" class="btn btn-header-login-modal" data-bs-toggle="modal" data-bs-target="#ModalForm">
    <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" fill="#608762" class="bi bi-person-circle" viewBox="0 0 16 16">
        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
    </svg>
</button>
<div class="modal fade" id="ModalForm" tabindex="-1" aria-labelledby="ModalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="myform">
                    <h1 class="text-center">Connection</h1>
                    <form method="POST" action="{{ route('login') }}">
                        <div class="mb-3 mt-4">
                            <label for="exampleInputEmail1" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Mots de passe</label>
                            <input type="password" name="password" class="form-control" id="exampleInputPassword1">
                        </div>
                        <div class="d-flex flex-wrap">
                            <div class="flex items-center justify-end mt-4">
                                <a href="{{ route('login.google') }}">
                                    <img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png">
                                </a>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <a href="{{ route('login.facebook') }}">
                                    <img src="/imgs/fb_logo.png" style="margin-left: 3em;width: 45px; height: 45px;">
                                </a>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-light mt-3">LOGIN</button>
                        <p>Not a member? <a href="#">Signup now</a></p>
{{--                        switch modal ??? --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

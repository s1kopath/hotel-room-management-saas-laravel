@extends('layouts.app')

@section('title', 'Test')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Test Content</h6>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Add your dashboard content here -->
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Position</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Office</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Age</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Start Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <img src="{{ asset('assets/img/team-2.jpg') }}"
                                                    class="avatar avatar-sm me-3" alt="user1">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">John Doe</h6>
                                                <p class="text-xs text-secondary mb-0">john.doe@example.com</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Software Engineer</p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">New York</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">30</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">2015-09-01</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Test Form</h6>
                    </div>
                </div>
                <div class="card-body">
                    <form role="form" class="text-start">
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required
                                autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-check form-switch d-flex align-items-center mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label mb-0 ms-3" for="remember">Remember me</label>
                        </div>

                        <div class="mb-1">
                            <label for="is_active" class="form-label">Status</label>
                            <select class="form-select select2 border rounded-3" id="is_active" name="is_active" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn bg-gradient-dark bg-brand w-100 my-4 mb-2">Sign in</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <button type="button" class="btn bg-gradient-dark bg-brand mb-0"
                onclick="loadModal('{{ route('load-modal') }}')">
                <i class="material-symbols-rounded text-sm">add</i>&nbsp;&nbsp;Add New
            </button>
        </div>
    </div>

    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Launch Modal
    </button>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form role="form">
                        <div class="mb-3">
                            <div class="text-center">
                                <img src="{{ asset('assets/img/user-dummy-img.jpg') }}" class="img-fluid" width="100px"
                                    id="img-id" alt="Driver Image">
                            </div>
                            <input type="file" name="profile_photo_path" class="form-control" id="profile_photo_path"
                                onchange="previewThumbnail(this, 'img-id')">
                        </div>
                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Select</label>
                            <select class="form-select select2 border rounded-3">
                                <option value="1">Select 1</option>
                                <option value="2">Select 2</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-dark bg-brand" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn bg-gradient-dark bg-brand">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

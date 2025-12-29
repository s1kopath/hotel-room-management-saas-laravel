@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
    <div class="text-end mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSettingModal">
            <i class="material-symbols-rounded text-sm">add</i>&nbsp;&nbsp;Add Setting
        </button>
    </div>
    <div class="card">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">System Settings</h6>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Setting Key</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Value</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Description</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($settings as $setting)
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $setting->setting_key }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control" 
                                        name="settings[{{ $setting->setting_key }}]" 
                                        value="{{ $setting->setting_value }}" 
                                        style="max-width: 300px;">
                                </td>
                                <td>
                                    <p class="text-xs text-secondary mb-0">{{ $setting->description }}</p>
                                </td>
                                <td class="align-middle">
                                    <form action="{{ route('admin.settings.destroy', $setting->id) }}" 
                                        method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger text-xs mb-0" 
                                            onclick="return confirm('Are you sure you want to delete this setting?')">
                                            <i class="material-symbols-rounded">delete</i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Setting Modal -->
    <div class="modal fade" id="addSettingModal" tabindex="-1" aria-labelledby="addSettingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.settings.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSettingModalLabel">Add System Setting</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="setting_key" class="form-label">Setting Key
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('setting_key') is-invalid @enderror" 
                                id="setting_key" name="setting_key" 
                                value="{{ old('setting_key') }}" 
                                placeholder="e.g. max_upload_size_mb" required>
                            <small class="text-muted">Use lowercase with underscores (snake_case)</small>
                            @error('setting_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="setting_value" class="form-label">Value</label>
                            <input type="text" class="form-control @error('setting_value') is-invalid @enderror" 
                                id="setting_value" name="setting_value" 
                                value="{{ old('setting_value') }}" 
                                placeholder="Setting value">
                            @error('setting_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="2" 
                                placeholder="What this setting does">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Setting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


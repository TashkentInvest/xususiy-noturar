@extends('layouts.admin')

@section('content')
<!-- Content Header (Page header) -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">@lang('cruds.regions_districts.districts.title')</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #007bff;">@lang('global.home')</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('districtIndex') }}" style="color: #007bff;">@lang('cruds.regions_districts.districts.title')</a></li>
                    <li class="breadcrumb-item active">@lang('global.edit')</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<!-- Main content -->
<div class="row">
    <div class="col-8 offset-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">@lang('global.edit')</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <form action="{{ route('districtUpdate', $district->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <div class="row">
                            <div class="col-12">
                                <label>@lang('cruds.regions_districts.regions.title')</label>
                                <select class="form-control select2" style="width: 100%;" name="region_id" required>
                                    <option value="" disabled selected>@lang('cruds.regions_districts.districts.select_region')</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ $region->id == old('region_id', $district->region_id) ? 'selected' : '' }}>{{ $region->{'name_' . $locale} }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @foreach(config('constants.locales') as $locale)
                                <div class="col-12 col-lg-6 mb-2">
                                    <label>@lang('global.name_as')  {{ $locale['title'] }}</label>
                                    <input type="text" name="name_{{ $locale['short_name'] }}" class="form-control" 
                                    value="{{ old('name_' . $locale['short_name']) ?? $district->{'name_' . $locale['short_name']} }}" placeholder="Название" required>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group ">
                        <button type="submit" class="btn btn-success waves-effect waves-light float-right">@lang('global.save')</button>
                        <a href="{{ route('districtIndex') }}" class="btn btn-light waves-effect float-left">@lang('global.cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
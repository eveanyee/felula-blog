@extends('layouts.app')

@section('body')

<div class="container py-4">
    <div class="row">

        @include('partials.back-link')

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Upload Post
                </div>

                <div class="card-body">
<form action="{{ route('user.posts.storeupload') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                        <div class="form-group">
                            <label for="">Select CSV file to import</label>
                                    <input id="csv_file" type="file" class="form-control" name="csv_file" required>

                            @error('csv_file')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span> 
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                            <input type="checkbox" name="header" checked> File contains header row?
                                    </label>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="">Categories</label>
                            <select name="category" class="form-control">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach 
                            </select>

                            @error('category')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span> 
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="publish" id="publish-post" checked>
                                <label class="custom-control-label" for="publish-post">Do you want to publish this post?</label>
                            </div>
                        </div>

                        <button class="btn btn-primary" type="submit">Upload</button>

                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div>


@endsection

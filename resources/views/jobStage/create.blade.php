    {{Form::open(array('url'=>'job-stage','method'=>'post', 'class'=>'needs-validation', 'novalidate'))}}
    <div class="modal-body">

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('title',__('Title'),['class'=>'form-label'])}}<x-required></x-required>
                {{Form::text('title',null,array('class'=>'form-control','placeholder'=>__('Enter stage title'),'required'=> 'required'))}}
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
    {{Form::close()}}


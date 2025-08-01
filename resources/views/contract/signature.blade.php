<form id='form_pad' method="post" enctype="multipart/form-data">
    @method('POST')
    <div class="modal-body" id="">
        <div class="row">
         @csrf
            <input type="hidden" name="contract_id" value="{{$contract->id}}">
            <div class="form-control" >
                <canvas id="signature-pad" class="signature-pad" height=200 ></canvas>
                <input type="hidden" @if(Auth::user()->type == 'company')name="company_signature" @elseif(Auth::user()->type == 'client' ) name="client_signature" @endif id="SignupImage1">
            </div>
            <div class="mt-1">
               <button type="button" class="btn btn-sm btn-secondary" id="clearSig">{{__('Clear')}}</button>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary " data-bs-dismiss="modal">
        <input type="button" id="addSig" value="{{__('Sign')}}" class="btn btn-primary ms-2">
    </div>
</form>

<script src="{{asset('assets/js/plugins/signature_pad/signature_pad.min.js')}}"></script>
<script>
    var signature = {
        canvas: null,
        clearButton: null,

        init: function init() {

            this.canvas = document.querySelector(".signature-pad");
            this.clearButton = document.getElementById('clearSig');
            this.saveButton = document.getElementById('addSig');
                signaturePad = new SignaturePad(this.canvas);

                this.clearButton.addEventListener('click', function (event) {

                    signaturePad.clear();
                });

                this.saveButton.addEventListener('click', function (event) {
                    var data = signaturePad.toDataURL('image/png');
                    $('#SignupImage1').val(data);

                    $.ajax({
                    url: '{{route("signaturestore")}}',
                    type: 'POST',
                    data: $("form").serialize(),
                    success: function (data) {
                        location.reload();
                        toastrs('success', data.message,'success');
                        $("#exampleModal").modal('hide');
                    },
                    error: function (data)
                    {
                    }
                });
                });

        }
    };
    signature.init();

</script>

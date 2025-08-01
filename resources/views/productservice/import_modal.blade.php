<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <div id="process_area" class="overflow-auto import-data-table">
            </div>
        </div>
        <div class="form-group col-12 d-flex justify-content-end col-form-label">
            <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary cancel" data-bs-dismiss="modal">
            <button type="submit" name="import" id="import" class="btn btn-primary ms-2" disabled>{{__('Import')}}</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var total_selection = 0;
        var first_name = 0;
        var last_name = 0;
        var email = 0;
        var column_data = [];
        var data = {};

        $('.cancel').on('click', function () {
            location.reload();
        });

        $(document).on('change', '.set_column_data', function() {
            var column_data = {};
            var column_name = $(this).val();
            var column_number = $(this).data('column_number');

            $('.set_column_data').each(function() {
                var col_num = $(this).data('column_number');
                var selected = $(this).val();

                if (selected !== '') {
                    column_data[selected] = col_num;
                }
            });


            $('.set_column_data').each(function() {
                var $this = $(this);
                var col_num = $this.data('column_number');

                $this.find('option').each(function() {
                    var option_value = $(this).val();

                    if (option_value !== '' && option_value in column_data && column_data[option_value] !== col_num) {
                        $(this).prop('hidden', true);
                    } else {
                        $(this).prop('hidden', false);
                    }
                });
            });

            total_selection = Object.keys(column_data).length;

            if (total_selection == 12) {
                $("#import").removeAttr("disabled");
                data = {
                    name: column_data.name,
                    sku: column_data.sku,
                    sale_price: column_data.sale_price,
                    purchase_price: column_data.purchase_price,
                    quantity: column_data.quantity,
                    description: column_data.description,
                    type: [],
                    sale_chartaccount_id: [],
                    expense_chartaccount_id: [],
                    tax_id: [],
                    category_id: [],
                    unit_id: [],
                };
            } else {
                $('#import').attr('disabled', 'disabled');
            }
        });

        $("#submit").click(function() {
            $(".doc_data").each(function() {
                if (!isNaN(this.value)) {
                    var id = '#doc_validation-' + $(this).data("key");
                    $(id).removeClass('d-none')
                    return false;
                }
            });
        });

        $(document).on('click', '#import', function(event) {

            event.preventDefault();
            $(".type").each(function() {
                data.type.push($(this).val());
            });
            $(".sale_chartaccount_id").each(function() {
                data.sale_chartaccount_id.push($(this).val());
            });
            $(".expense_chartaccount_id").each(function() {
                data.expense_chartaccount_id.push($(this).val());
            });
            $(".tax_id").each(function() {
                data.tax_id.push($(this).val());
            });
            $(".category_id").each(function() {
                data.category_id.push($(this).val());
            });
            $(".unit_id").each(function() {
                data.unit_id.push($(this).val());
            });
            data._token = "{{ csrf_token() }}";

            
            $.ajax({
                url: "{{ route('productservice.import.data') }}",
                method: "POST",
                data: data,
                beforeSend: function() {
                    $('#import').attr('disabled', 'disabled');
                    $('#import').text('Importing...');
                },
                success: function(data) {
                    $('#import').attr('disabled', false);
                    $('#import').text('Import');
                    $('#upload_form')[0].reset();

                    if (data.html == true) {
                        $('#process_area').html(data.response);
                        $("button").hide();
                        show_toastr('Error', __('This data has not been inserted.'), 'error');

                    } else {
                        $('#message').html(data.response);
                        $('#commonModalOver').modal('hide')
                        show_toastr('Success', data.response, 'Success');
                    }

                }
            })

        });
        $('#commonModalOver').on('hidden.bs.modal', function () {
            location.reload();
        });
    });
</script>

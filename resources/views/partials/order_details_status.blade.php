<script id="details-template" type="text/x-handlebars-template">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Order Details</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-12"><label>PO Number:</label></div>
                    <div class="col-md-12"><label style="font-weight:bold">@{{ po_number }}</label></div>
                    <div class="col-md-12"><label>Customer:</label></div>
                    <div class="col-md-12"><label style="font-weight:bold">@{{ customer }}</label></div>
                    <div class="col-md-12"><label>Remaing Credits:</label></div>
                    <div class="col-md-12"><label style="font-weight:bold">{{ PESO_SYMBOL }} @{{ credits }}</label></div>

                    <div class="col-md-12">
                        @{{#ifCond status '==' 'Pending'}}
                        <p>Status:<br/><strong>Pending</strong></p>
                        @{{/ifCond}}
                        @{{#ifCond status '==' 'Cancelled'}}
                        <div role="alert" class="alert alert-warning">
                            <p>Status:<br/><strong>Cancelled</strong></p>
                            <p>Reason:<br/><strong>@{{ extra }}</strong></p>
                        </div>
                        @{{/ifCond}}
                        @{{#ifCond status '==' 'Disapproved'}}
                        <div role="alert" class="alert alert-danger">
                            <p>Status:<br/><strong>Disapproved</strong></p>
                            <p>Disapproved by:<br/><strong>@{{ user }}</strong></p>
                            <p>Comment:<br/><strong>@{{ extra }}</strong></p>
                        </div>
                        @{{/ifCond}}
                        @{{#ifCond status '==' 'Approved'}}
                        <div role="alert" class="alert alert-success">
                            <p>Status:<br/><strong>Approved</strong></p>
                            <p>Approved by:<br/><strong>@{{ user }}</strong></p>
                            <p>Comment:<br/><strong>@{{ extra }}</strong></p>
                        </div>
                        @{{/ifCond}}
                    </div>

                    @{{#ifCond updated_by '!=' null}}
                    <div class="col-md-12"><label>Updated By:</label></div>
                    <div class="col-md-12"><label style="font-weight:bold">@{{ updated_by }}</label></div>
                    <div class="col-md-12"><label>Date:</label></div>
                    <div class="col-md-12"><label style="font-weight:bold">@{{ updated_at }}</label></div>
                    <div class="col-md-12"><label>Remarks:</label></div>
                    <div class="col-md-12"><textarea readonly style="resize:none;width:100%" rows="5">@{{ update_remarks }}</textarea></div>
                    @{{/ifCond}}
                </div>
            </div>
            <div class="col-md-9">
                <label for="">Order Items:</label>
                <table class="table table-striped">
                    <thead>
                    <th>Product</th>
                    <th>Category</th>
                    <th>UOM</th>
                    <th>Unit Price ({{ PESO_SYMBOL }})</th>
                    <th>Quantity</th>
                    <th>Price ({{ PESO_SYMBOL }})</th>
                    </thead>
                    <tbody>
                    @{{#each details}}
                    <tr class="@{{#oddEven @index}}@{{/oddEven}}">
                        <td>@{{ product }}</td>
                        <td>@{{ category }}</td>
                        <td>@{{ uom }}</td>
                        <td class="text-right">@{{ unit_price }}</td>
                        <td class="text-right">@{{ quantity }}</td>
                        <td class="text-right">@{{ price }}</td>
                    </tr>
                    @{{/each}}
                    </tbody>
                    <tfoot>
                    <th colspan="6">
                        <p class="text-right" style="font-weight:bold">Total: {{ PESO_SYMBOL }} @{{ total }}</p>
                    </th>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        @{{#ifCond status '==' 'Pending'}}
        @if ($role == 'Approver')
        <a href="{{ url('orders') }}/@{{ id }}/edit/approver" id="btn_edit" class="btn btn-default"><span class="glyphicon glyphicon-edit"></span> Edit</a>
        <a class="btn btn-default btn-approve" data-order-id="@{{ id }}" data-po-number="@{{ po_number }}" data-status="Approved" href="#" title="Approve"><span class="glyphicon glyphicon-ok"></span> Approve</a>
        <a class="btn btn-default btn-disapprove" data-order-id="@{{ id }}" data-po-number="@{{ po_number }}" data-status="Disapproved" href="#" title="Disapprove"><span class="glyphicon glyphicon-remove"></span> Disapprove</a>
        @endif

        @if ($role == 'Sales' || $role == 'Administrator')
        <a class="btn btn-default" href="{{ url('orders') }}/@{{ id }}/edit" id="btn_edit" title="Edit"><span class="glyphicon glyphicon-edit"></span> Edit</a>
        <a class="btn btn-default btn-cancel" data-order-id="@{{ id }}" data-po-number="@{{ po_number }}" data-status="Cancelled" href="#" title="Cancel"><span class="glyphicon glyphicon-remove"></span> Cancel</a>
        @endif
        @{{/ifCond}}
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</script>

<script id="update-order-status-template" type="text/x-handlebars-template">
    {!! Form::open(['url' => '', 'name' => 'update_order_status_form', 'id' => 'update_order_status_form', 'method' => 'PUT']) !!}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Update Order Status</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-12"><label>PO Number:</label></div>
                    <div class="col-md-12"><label style="font-weight:bold">@{{ po_number }}</label></div>
                    <div class="col-md-12"><label>Change Status To:</label></div>
                    <div class="col-md-12"><label style="font-weight:bold">@{{ status }}</label></div>
                </div>
            </div>
            <div class="col-md-9">
                <label id="lbl_extra" for="extra">Optional Message:</label>
                <textarea id="extra" name="extra" class="form-control" cols="30" rows="10"></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn btn-default btn-back" data-order-id="@{{ id }}" value="Back">
        <input type="submit" class="btn btn-default" value="Confirm">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
    {!! Form::close() !!}
</script>

<script>
    var update_order_status_template = Handlebars.compile($("#update-order-status-template").html());
    var details_template = Handlebars.compile($("#details-template").html());

    function showDetailsModal(id) {
        $.get('{{ url('get-order-details') }}/' + id, {},
            function(data){
                // Display order details to modal
                var details = JSON.parse(data);
                // Add order id of selected tr
                details.id = id;
                $($modal.find('div.modal-content')[0]).html(details_template(details));
                $modal.modal('show');
            }).fail(function(){
            alert('Failed to get details.');
        });
    }

    // Update status buttons click event
    $(document).on('click', 'a.btn-approve, a.btn-disapprove, a.btn-cancel', function(){
        var status = $(this).attr('data-status');

        $($modal.find('div.modal-content')[0]).html(update_order_status_template({
            po_number: $(this).attr('data-po-number'),
            id: $(this).attr('data-order-id'),
            status: status
        }));

        var $lbl_extra = $('#lbl_extra'),
            $extra = $('#extra'),
            url = '{{ url('orders')  }}/' + $(this).attr('data-order-id') + '/update-{{ $role == 'Approver' ? 'approver' : 'customer' }}-status/' + status;

        // Update url of form
        $('form#update_order_status_form').attr('action', url);

        // If disapproval/cancellation, require reason
        if (status == 'Cancelled' || status == 'Disapproved'){
            $lbl_extra.html('Reason:').addClass('required');
            $extra.attr('required', 'required');
        } else {
            $lbl_extra.html('Optional message:').removeClass('required');
            $extra.removeAttr('required');
        }
    });

    // Back button
    $(document).on('click', 'input.btn-back', function(){
        showDetailsModal($(this).attr('data-order-id'));
    });

    // View details click event
    $(document).on('click', '.table-order-view tbody tr', function() {
        showDetailsModal($(this).attr('data-order-id'));

        $('.selected').removeClass('selected');
        $(this).addClass('selected');
    });
</script>
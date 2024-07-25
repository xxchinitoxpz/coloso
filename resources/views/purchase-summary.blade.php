<!-- Modal HTML -->
<div id="summaryModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Purchase Summary</h2>
        <div id="summaryDetails"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('button[name="Show Summary"]').on('click', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("purchase.showSummary") }}',
            type: 'POST',
            data: {
                products: $('input[name^="products"]').map(function() { return $(this).val(); }).get(),
                quantities: $('input[name^="quantities"]').map(function() { return $(this).val(); }).get(),
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                var summaryHtml = '<table><thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr></thead><tbody>';
                $.each(data.summaryData, function(index, item) {
                    summaryHtml += '<tr><td>' + item.name + '</td><td>' + item.price + '</td><td>' + item.quantity + '</td><td>' + item.subtotal + '</td></tr>';
                });
                summaryHtml += '<tr><td colspan="3">Total</td><td>' + data.total + '</td></tr></tbody></table>';
                $('#summaryDetails').html(summaryHtml);
                $('#summaryModal').show();
            }
        });
    });

    $('.close').on('click', function() {
        $('#summaryModal').hide();
    });

    $(window).on('click', function(event) {
        if ($(event.target).is('#summaryModal')) {
            $('#summaryModal').hide();
        }
    });
});
</script>

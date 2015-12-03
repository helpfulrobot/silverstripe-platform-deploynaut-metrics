var charts = [];

$(document).ready(function() {
    $('[data-metric]').each(function(i) {
        charts[i] = c3.generate({
            bindto: this,
            data: {
                x: 'x',
                columns: $(this).data('points'),
                types: {
                    sum: 'bar'
                }
            },
            axis: {
                x: {
                    tick: {
                        format: function(x) { return moment.unix(x).format('ddd h:mma') },
                        culling: {
                            max: 6
                        }
                    }
                }
            }
        });
    });
});
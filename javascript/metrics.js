var charts = [];

console.log("hello");




$(document).ready(function() {
    $('[data-metric]').each(function(i) {
      var cols = [];
      $(this).data('points').forEach(function(query, i, queries) {
        data = query.data;
        data.unshift(query.name);
        cols.push(data);
      });
      console.log(cols);
        charts[i] = c3.generate({
            bindto: this,
            data: {
                x: 'x',
                columns: cols,
                types: {
                    sum: 'bar'
                }
							},
						color: {
								pattern: ['#3AAEF9', '#303E4D']
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

<div class="content page-header">
    <div class="row">
        <div class="col-md-12">
            <% include Breadcrumb %>
            <% include DeploymentTabs %>
            <% include ProjectLinks %>
        </div>
    </div>
</div>

<div class="content">

    <div id="rangeheader">
        <div id="rangedropdown">
            <div id="picker">$Range</div>
            <a class="btn btn-primary">
                <i class="fa fa-clock-o">
                </i>
             Set Range
            </a>
        </div>
    </div>

    <div class="metric-set">
        <div class="metrics">
        <% loop $Metrics %>
            <h3>$Name</h3>
            <div class="metrics">
                <div class="metric">
                    <div height="200" class="chart requests" data-metric data-display="$ChartType" data-points='$Up.getData($ID)'></div>
                    <p>$Description</p>
                </div>
                <hr>
        <% end_loop %>
        </div>
    </div>
</div>


<% require css('deploynaut-rainforest-metrics/css/vendor/c3.min.css') %>
<% require css('deploynaut-rainforest-metrics/css/metrics.css') %>
<% require javascript('deploynaut-rainforest-metrics/javascript/vendor/moment.js') %>
<% require javascript('deploynaut-rainforest-metrics/javascript/vendor/d3.min.js') %>
<% require javascript('deploynaut-rainforest-metrics/javascript/vendor/c3.min.js') %>
<% require javascript('deploynaut-rainforest-metrics/javascript/metrics.js') %>
<% require javascript('deploynaut-rainforest-metrics/javascript/timepicker.js') %>

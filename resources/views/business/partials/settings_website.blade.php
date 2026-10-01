<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-8">
            <h4>DHC Shoes website</h4>
            <p>The POS and the website use separate databases. This screen calls the website catalog API. The token stays in the .env file and is not shown here.</p>
            <table class="table">
                <tr>
                    <th style="width:180px;">Website URL</th>
                    <td>{{ config('store_api.website_url') ?: 'Not set' }}</td>
                </tr>
                <tr>
                    <th>API</th>
                    <td>{{ rtrim((string) config('store_api.website_url'), '/') }}/api/pos/catalog</td>
                </tr>
                <tr>
                    <th>Token</th>
                    <td>{{ config('store_api.token') ? 'Set' : 'Missing' }}</td>
                </tr>
            </table>
            <button type="button" class="btn btn-primary" id="test_website_btn">Test connection</button>
        </div>
    </div>
</div>

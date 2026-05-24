<div class="modal fade" id="transaction-import-modal" tabindex="-1" aria-labelledby="transaction-import-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transaction-import-modal-title">Import transactions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('transactions.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body space-y-4">
                    <p class="text-sm text-muted-foreground mb-0">
                        Upload a spreadsheet with columns: <strong>Date</strong>, <strong>Title</strong>, <strong>Type</strong>, and <strong>Amount</strong>.
                        Optional: Description and Category (must match an existing category name).
                    </p>
                    <ul class="text-sm text-muted-foreground mb-0 ps-3">
                        <li>Types: income, expense, asset, or liability</li>
                        <li>Dates: YYYY-MM-DD or common formats (e.g. 01/15/2026)</li>
                        <li>Max {{ number_format(\App\Services\TransactionImportService::MAX_ROWS) }} rows per file</li>
                    </ul>
                    <div>
                        <x-ui.label for="transaction-import-file">Excel or CSV file</x-ui.label>
                        <x-ui.input
                            type="file"
                            name="file"
                            id="transaction-import-file"
                            accept=".xlsx,.csv,text/csv"
                            required
                        />
                        @error('file')
                            <x-ui.field-error :messages="[$message]" />
                        @enderror
                    </div>
                    <p class="mb-0">
                        <a href="{{ route('transactions.import.template') }}" class="text-sm font-medium text-primary">
                            Download sample template (.xlsx)
                        </a>
                    </p>
                </div>
                <div class="modal-footer">
                    <x-ui.button variant="outline" type="button" data-bs-dismiss="modal">Cancel</x-ui.button>
                    <x-ui.button type="submit">Import</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</div>

@props([
    'placeholder' => 'Search clients, lots, deceased, reservations...',
    'minLength' => 2,
])

<div class="global-search-wrapper" x-data="globalSearch({{ $minLength }})" x-init="init()">
    <div class="position-relative">
        <div class="global-search-input-wrap">
            <i data-feather="search" class="global-search-icon"></i>
            <input 
                type="text" 
                id="globalSearchInput"
                class="form-control global-search-input" 
                :class="{ 'has-results': showResults && results.length > 0 }"
                placeholder="{{ $placeholder }}"
                x-model="query"
                @input.debounce.300ms="search()"
                @focus="showResults = true"
                @keydown.escape="close()"
                @keydown.enter.prevent="goToFirst()"
                @keydown.up.prevent="navigateUp()"
                @keydown.down.prevent="navigateDown()"
                autocomplete="off"
            >
            <button 
                type="button" 
                class="btn btn-link global-search-clear p-0 border-0" 
                x-show="query.length > 0"
                @click="clearSearch()"
                x-cloak
            >
                <i data-feather="x" style="height: 14px; width: 14px;"></i>
            </button>
        </div>
        
        <div 
            class="global-search-dropdown"
            x-show="showResults && (isLoading || results.length > 0 || noResults)"
            x-cloak
            @click.away="close()"
        >
            <div class="global-search-loading" x-show="isLoading">
                <div class="d-flex justify-content-center py-3">
                    <div class="spinner-border spinner-border-sm text-muted" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            
            <template x-if="!isLoading && results.length > 0">
                <div>
                    <template x-for="(group, index) in groupedResults" :key="index">
                        <div class="global-search-group">
                            <div class="global-search-group-header">
                                <span x-text="group.label"></span>
                                <span class="badge bg-light text-dark" x-text="group.items.length"></span>
                            </div>
                            <template x-for="(item, itemIndex) in group.items" :key="item.id">
                                <a 
                                    :href="item.url"
                                    class="global-search-item"
                                    :class="{ 'active': isSelected(group.name, item.id) }"
                                    @mouseenter="setSelected(group.name, item.id)"
                                    @click="close()"
                                >
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="global-search-icon-wrap" :class="'icon-' + item.type">
                                            <i data-feather="circle" style="height: 14px; width: 14px;"></i>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="global-search-item-title" x-text="item.title"></div>
                                            <div class="global-search-item-subtitle" x-text="item.subtitle"></div>
                                        </div>
                                        <div class="global-search-meta" x-show="item.meta" x-text="item.meta"></div>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                    
                    <div class="global-search-footer">
                        <span class="text-muted small">
                            <span x-text="results.length"></span> result(s)
                        </span>
                        <kbd>Esc</kbd> to close
                    </div>
                </div>
            </template>
            
            <div class="global-search-no-results" x-show="!isLoading && noResults">
                <div class="text-center py-4 text-muted">
                    <i data-feather="search" style="height: 32px; width: 32px; opacity: 0.3;"></i>
                    <p class="mb-0 mt-2">No results found</p>
                    <p class="small">Try a different search term</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .global-search-wrapper {
        position: relative;
        width: 100%;
        max-width: 400px;
    }
    
    .global-search-input-wrap {
        position: relative;
    }
    
    .global-search-icon {
        position: absolute;
        top: 50%;
        left: 12px;
        transform: translateY(-50%);
        color: #94a3b8;
        height: 16px;
        width: 16px;
        pointer-events: none;
    }
    
    .global-search-input {
        padding-left: 40px !important;
        padding-right: 36px !important;
        background: rgba(255, 255, 255, 0.9) !important;
        border: 1px solid #e2e8f0 !important;
        height: 38px;
        font-size: 0.875rem;
    }
    
    .global-search-input:focus {
        background: #fff !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }
    
    .global-search-input.has-results {
        border-bottom-left-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    
    .global-search-clear {
        position: absolute;
        top: 50%;
        right: 8px;
        transform: translateY(-50%);
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
    }
    
    .global-search-clear:hover {
        background: #f1f5f9;
        color: #64748b;
    }
    
    .global-search-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-top: none;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
        max-height: 400px;
        overflow-y: auto;
        z-index: 1050;
    }
    
    .global-search-group {
        border-bottom: 1px solid #f1f5f9;
    }
    
    .global-search-group:last-of-type {
        border-bottom: none;
    }
    
    .global-search-group-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        background: #f8fafc;
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
    }
    
    .global-search-item {
        display: block;
        padding: 10px 12px;
        color: inherit;
        text-decoration: none;
        border-left: 3px solid transparent;
    }
    
    .global-search-item:hover,
    .global-search-item.active {
        background: #f8fafc;
        border-left-color: #3b82f6;
    }
    
    .global-search-icon-wrap { color: #6366f1; }
    .global-search-icon-wrap.icon-client { color: #3b82f6; }
    .global-search-icon-wrap.icon-deceased { color: #ec4899; }
    .global-search-icon-wrap.icon-lot { color: #10b981; }
    .global-search-icon-wrap.icon-reservation { color: #f59e0b; }
    
    .global-search-item-title {
        font-weight: 500;
        color: #1e293b;
        font-size: 0.875rem;
    }
    
    .global-search-item-subtitle {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 2px;
    }
    
    .global-search-meta {
        font-size: 0.7rem;
        color: #94a3b8;
        background: #f1f5f9;
        padding: 2px 6px;
        border-radius: 4px;
    }
    
    .global-search-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        font-size: 0.7rem;
        color: #94a3b8;
    }
    
    .global-search-footer kbd {
        background: #e2e8f0;
        padding: 2px 6px;
        border-radius: 4px;
    }
    
    [x-cloak] { display: none !important; }
    
    @media (max-width: 991.98px) {
        .global-search-wrapper { max-width: 100%; }
        .global-search-dropdown {
            position: fixed;
            top: 60px;
            left: 10px;
            right: 10px;
            max-height: calc(100vh - 80px);
        }
    }
</style>
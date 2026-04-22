document.addEventListener('alpine:init', function() {
    Alpine.data('globalSearch', function(minLength) {
        return {
            query: '',
            results: [],
            isLoading: false,
            showResults: false,
            selectedId: null,
            selectedGroup: null,
            minLength: minLength || 2,
            
            get noResults() {
                return this.query.length >= this.minLength && !this.isLoading && this.results.length === 0;
            },
            
            get groupedResults() {
                const groups = {};
                const groupLabels = {
                    client: 'Clients',
                    deceased: 'Deceased',
                    lot: 'Lots',
                    reservation: 'Reservations'
                };
                
                this.results.forEach(item => {
                    if (!groups[item.type]) {
                        groups[item.type] = {
                            name: item.type,
                            label: groupLabels[item.type] || item.type,
                            items: []
                        };
                    }
                    groups[item.type].items.push(item);
                });
                
                return Object.values(groups);
            },
            
            init() {
                this.$nextTick(() => {
                    if (window.feather && typeof window.feather.replace === 'function') {
                        window.feather.replace();
                    }
                });
            },
            
            async search() {
                if (this.query.length < this.minLength) {
                    this.results = [];
                    return;
                }
                
                this.isLoading = true;
                
                try {
                    const response = await fetch('/api/search?q=' + encodeURIComponent(this.query));
                    const data = await response.json();
                    this.results = data.results || [];
                } catch (error) {
                    console.error('Search error:', error);
                    this.results = [];
                } finally {
                    this.isLoading = false;
                }
            },
            
            close() {
                this.showResults = false;
                this.selectedId = null;
                this.selectedGroup = null;
            },
            
            clearSearch() {
                this.query = '';
                this.results = [];
                this.showResults = false;
                const input = document.getElementById('globalSearchInput');
                if (input) input.focus();
            },
            
            navigateUp() {
                const flat = this.flattenResults();
                if (flat.length === 0) return;
                
                const currentIndex = flat.findIndex(r => r.id === this.selectedId);
                if (currentIndex > 0) {
                    this.selectedId = flat[currentIndex - 1].id;
                    this.selectedGroup = flat[currentIndex - 1].type;
                }
            },
            
            navigateDown() {
                const flat = this.flattenResults();
                if (flat.length === 0) return;
                
                const currentIndex = flat.findIndex(r => r.id === this.selectedId);
                if (currentIndex < flat.length - 1) {
                    this.selectedId = flat[currentIndex + 1].id;
                    this.selectedGroup = flat[currentIndex + 1].type;
                }
            },
            
            flattenResults() {
                return this.results;
            },
            
            setSelected(group, id) {
                this.selectedGroup = group;
                this.selectedId = id;
            },
            
            isSelected(group, id) {
                return this.selectedGroup === group && this.selectedId === id;
            },
            
            goToFirst() {
                if (this.results.length > 0) {
                    window.location.href = this.results[0].url;
                }
            }
        };
    });
});

function toggleMobileSearch() {
    const searchWrap = document.querySelector('.global-search-wrapper');
    if (searchWrap) {
        searchWrap.classList.toggle('mobile-active');
        const input = document.getElementById('globalSearchInput');
        if (input) {
            if (searchWrap.classList.contains('mobile-active')) {
                input.focus();
            }
        }
    }
}
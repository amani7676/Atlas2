/**
 * Performance Optimization for Tablelists Page
 * Optimized for Production Hosting Environment
 */

class TableListOptimizer {
    constructor() {
        this.init();
        this.setupDebouncing();
        this.setupLazyLoading();
        this.setupClientSideCaching();
        this.setupOptimizedEventListeners();
    }

    init() {
        // Performance monitoring
        this.performanceMetrics = {
            modalOpenTime: 0,
            inlineEditTime: 0,
            cacheHits: 0,
            cacheMisses: 0
        };

        // Client-side cache
        this.clientCache = new Map();
        
        // Debounce timers
        this.debounceTimers = new Map();
        
        console.log('TableList Optimizer initialized');
    }

    setupDebouncing() {
        // Debounce function for inline edits
        window.debounceInlineEdit = (callback, delay = 300) => {
            return (residentId) => {
                const timerId = `inline_edit_${residentId}`;
                
                if (this.debounceTimers.has(timerId)) {
                    clearTimeout(this.debounceTimers.get(timerId));
                }
                
                const timer = setTimeout(() => {
                    const startTime = performance.now();
                    callback(residentId);
                    this.performanceMetrics.inlineEditTime = performance.now() - startTime;
                }, delay);
                
                this.debounceTimers.set(timerId, timer);
            };
        };

        // Debounce phone number formatting
        window.debouncePhoneFormat = (callback, delay = 200) => {
            return (value, key) => {
                const timerId = `phone_format_${key}`;
                
                if (this.debounceTimers.has(timerId)) {
                    clearTimeout(this.debounceTimers.get(timerId));
                }
                
                const timer = setTimeout(() => {
                    callback(value, key);
                }, delay);
                
                this.debounceTimers.set(timerId, timer);
            };
        };
    }

    setupLazyLoading() {
        // Lazy load modal content
        window.lazyLoadModal = (modalType, residentId = null) => {
            const startTime = performance.now();
            
            // Show loading state immediately
            this.showModalLoading(modalType);
            
            // Simulate network delay for better UX
            setTimeout(() => {
                this.performanceMetrics.modalOpenTime = performance.now() - startTime;
                
                // Trigger actual modal loading
                if (modalType === 'edit' && residentId) {
                    Livewire.find('tablelists').call('editResident', residentId);
                } else if (modalType === 'details' && residentId) {
                    Livewire.find('tablelists').call('detailsChange', residentId);
                } else if (modalType === 'add') {
                    Livewire.find('tablelists').call('openAddModal', ...arguments);
                }
            }, 100);
        };
    }

    setupClientSideCaching() {
        // Cache frequently accessed data
        window.getCachedData = (key) => {
            if (this.clientCache.has(key)) {
                this.performanceMetrics.cacheHits++;
                return this.clientCache.get(key);
            }
            this.performanceMetrics.cacheMisses++;
            return null;
        };

        window.setCachedData = (key, data, ttl = 300000) => { // 5 minutes TTL
            this.clientCache.set(key, {
                data: data,
                timestamp: Date.now(),
                ttl: ttl
            });
        };

        // Clean expired cache entries
        setInterval(() => {
            const now = Date.now();
            for (const [key, value] of this.clientCache.entries()) {
                if (now - value.timestamp > value.ttl) {
                    this.clientCache.delete(key);
                }
            }
        }, 60000); // Clean every minute
    }

    setupOptimizedEventListeners() {
        // Optimized phone input handling
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('phone-input')) {
                const residentId = e.target.getAttribute('wire:model')?.split('.')[1];
                if (residentId) {
                    window.debouncePhoneFormat((value, key) => {
                        // Format phone number client-side for immediate feedback
                        let cleanValue = value.replace(/\D/g, '');
                        if (cleanValue.length >= 11 && cleanValue[0] === '0') {
                            cleanValue = cleanValue.substring(0, 4) + '-' + 
                                         cleanValue.substring(4, 7) + '-' + 
                                         cleanValue.substring(7, 11);
                        }
                        e.target.value = cleanValue;
                    }, 150)(e.target.value, residentId);
                }
            }
        });

        // Optimized button click handling
        document.addEventListener('click', (e) => {
            const button = e.target.closest('button');
            if (!button) return;

            // Fast action buttons
            if (button.classList.contains('fast-action-btn')) {
                e.preventDefault();
                
                // Add loading state immediately
                button.classList.add('loading');
                button.disabled = true;

                // Get action type from button attributes
                const residentId = button.getAttribute('data-resident-id');
                const action = button.getAttribute('data-action');

                // Use lazy loading for modals
                if (action === 'edit') {
                    window.lazyLoadModal('edit', residentId);
                } else if (action === 'details') {
                    window.lazyLoadModal('details', residentId);
                }

                // Remove loading state after delay
                setTimeout(() => {
                    button.classList.remove('loading');
                    button.disabled = false;
                }, 1000);
            }
        });

        // Optimized inline save handling
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[wire:model*="full_name"], input[wire:model*="payment_date"]')) {
                const residentId = e.target.getAttribute('wire:model')?.split('.')[1];
                if (residentId) {
                    window.debounceInlineEdit((id) => {
                        Livewire.find('tablelists').call('editResidentInline', id);
                    }, 500)(residentId);
                }
            }
        });
    }

    showModalLoading(modalType) {
        // Show immediate loading feedback
        const loadingHtml = `
            <div class="modal-loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">در حال بارگذاری...</p>
            </div>
        `;
        
        // You can customize this based on your modal structure
        console.log(`Loading ${modalType} modal...`);
    }

    // Performance monitoring
    getPerformanceMetrics() {
        return {
            ...this.performanceMetrics,
            cacheHitRate: this.performanceMetrics.cacheHits / 
                         (this.performanceMetrics.cacheHits + this.performanceMetrics.cacheMisses) * 100
        };
    }

    // Clear cache
    clearCache() {
        this.clientCache.clear();
        this.debounceTimers.forEach(timer => clearTimeout(timer));
        this.debounceTimers.clear();
    }
}

// Initialize optimizer when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.tableListOptimizer = new TableListOptimizer();
    
    // Log performance metrics every 30 seconds in development
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        setInterval(() => {
            const metrics = window.tableListOptimizer.getPerformanceMetrics();
            console.log('Performance Metrics:', metrics);
        }, 30000);
    }
});

// Export for global access
window.TableListOptimizer = TableListOptimizer;

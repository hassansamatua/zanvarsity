<?php
require_once 'includes/header.php';

// Get current tab or default to 'all'
$currentTab = isset($_GET['tab']) && in_array($_GET['tab'], ['all', 'filtered']) ? $_GET['tab'] : 'all';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Publications</h1>
            
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-4" id="publicationsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $currentTab === 'all' ? 'active' : '' ?>" 
                            id="all-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#all" 
                            type="button" 
                            role="tab" 
                            aria-controls="all" 
                            aria-selected="<?= $currentTab === 'all' ? 'true' : 'false' ?>">
                        All Publications
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $currentTab === 'filtered' ? 'active' : '' ?>" 
                            id="filtered-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#filtered" 
                            type="button" 
                            role="tab" 
                            aria-controls="filtered" 
                            aria-selected="<?= $currentTab === 'filtered' ? 'true' : 'false' ?>">
                        Filtered Publications
                    </button>
                </li>
            </ul>
            
            <!-- Tab Content -->
            <div class="tab-content" id="publicationsTabContent">
                <!-- All Publications Tab -->
                <div class="tab-pane fade <?= $currentTab === 'all' ? 'show active' : '' ?>" 
                     id="all" 
                     role="tabpanel" 
                     aria-labelledby="all-tab">
                    <div class="mb-4">
                        <a href="?tab=all&refresh=1" class="btn btn-primary" id="refreshAllBtn">
                            <i class="fas fa-sync-alt"></i> Refresh All Publications
                        </a>
                        <span id="lastUpdatedAll" class="text-muted ms-3"></span>
                    </div>
                    
                    <div id="loadingAll" class="text-center my-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading all publications...</p>
                    </div>
                    
                    <div id="publicationsListAll"></div>
                    <div id="paginationAll" class="d-flex justify-content-center mt-4"></div>
                </div>
                
                <!-- Filtered Publications Tab -->
                <div class="tab-pane fade <?= $currentTab === 'filtered' ? 'show active' : '' ?>" 
                     id="filtered" 
                     role="tabpanel" 
                     aria-labelledby="filtered-tab">
                    <div class="mb-4">
                        <a href="?tab=filtered&refresh=1" class="btn btn-primary" id="refreshFilteredBtn">
                            <i class="fas fa-sync-alt"></i> Refresh Filtered Publications
                        </a>
                        <span id="lastUpdatedFiltered" class="text-muted ms-3"></span>
                    </div>
                    
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Filter Publications</h5>
                        </div>
                        <div class="card-body">
                            <form id="filterForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="search" class="form-label">Search</label>
                                        <input type="text" class="form-control" id="search" placeholder="Search by title, author, or abstract">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="year" class="form-label">Year</label>
                                        <select class="form-select" id="year">
                                            <option value="">All Years</option>
                                            <?php
                                            $currentYear = date('Y');
                                            for ($year = $currentYear; $year >= 2000; $year--) {
                                                echo "<option value=\"$year\">$year</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div id="loadingFiltered" class="text-center my-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading filtered publications...</p>
                    </div>
                    
                    <div id="publicationsListFiltered"></div>
                    <div id="paginationFiltered" class="d-flex justify-content-center mt-4"></div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration
    const config = {
        all: {
            itemsPerPage: 10,
            currentPage: 1,
            publications: [],
            loadingElement: 'loadingAll',
            listElement: 'publicationsListAll',
            paginationElement: 'paginationAll',
            lastUpdatedElement: 'lastUpdatedAll',
            endpoint: 'all'
        },
        filtered: {
            itemsPerPage: 10,
            currentPage: 1,
            publications: [],
            loadingElement: 'loadingFiltered',
            listElement: 'publicationsListFiltered',
            paginationElement: 'paginationFiltered',
            lastUpdatedElement: 'lastUpdatedFiltered',
            endpoint: 'filtered'
        }
    };
    
    // Initialize tabs
    const tabEl = document.querySelector('button[data-bs-toggle="tab"]');
    if (tabEl) {
        tabEl.addEventListener('shown.bs.tab', function (event) {
            const tabId = event.target.getAttribute('aria-controls');
            if (config[tabId]) {
                loadPublications(tabId);
            }
        });
    }
    
    // Load publications for a specific tab
    function loadPublications(tabId, page = 1) {
        const tabConfig = config[tabId];
        if (!tabConfig) return;
        
        tabConfig.currentPage = page;
        const searchTerm = document.getElementById('search')?.value?.toLowerCase() || '';
        const yearFilter = document.getElementById('year')?.value || '';
        const refresh = new URLSearchParams(window.location.search).has('refresh');
        
        // Show loading indicator
        document.getElementById(tabConfig.loadingElement).style.display = 'block';
        document.getElementById(tabConfig.listElement).innerHTML = '';
        
        // Build API URL
        const url = new URL('publication.php', window.location.origin);
        url.searchParams.append('endpoint', tabConfig.endpoint);
        if (refresh) {
            url.searchParams.append('refresh', '1');
        }
        
        // Fetch publications
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    tabConfig.publications = Array.isArray(data.publications) ? data.publications : [];
                    displayPublications(tabId, tabConfig.publications, page, searchTerm, yearFilter);
                    updateLastUpdated(tabConfig, data.timestamp);
                } else {
                    throw new Error(data.message || 'Failed to load publications');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById(tabConfig.listElement).innerHTML = `
                    <div class="alert alert-danger">
                        Error loading ${tabId} publications: ${error.message}
                    </div>
                `;
            })
            .finally(() => {
                document.getElementById(tabConfig.loadingElement).style.display = 'none';
                
                // Remove refresh parameter from URL
                if (refresh) {
                    const url = new URL(window.location);
                    url.searchParams.delete('refresh');
                    window.history.replaceState({}, '', url);
                }
            });
    }
    
    // Display publications with pagination
    function displayPublications(tabId, publications, page, searchTerm = '', yearFilter = '') {
        const tabConfig = config[tabId];
        if (!tabConfig) return;
        
        // Apply filters
        let filtered = [...publications];
        
        if (searchTerm) {
            filtered = filtered.filter(pub => {
                const title = pub.title ? pub.title.toLowerCase() : '';
                const authors = pub.authors ? pub.authors.toLowerCase() : '';
                const abstract = pub.abstract ? pub.abstract.toLowerCase() : '';
                return title.includes(searchTerm) || 
                       authors.includes(searchTerm) || 
                       abstract.includes(searchTerm);
            });
        }
        
        if (yearFilter) {
            filtered = filtered.filter(pub => 
                pub.publication_date && pub.publication_date.startsWith(yearFilter)
            );
        }
        
        // Calculate pagination
        const totalItems = filtered.length;
        const totalPages = Math.ceil(totalItems / tabConfig.itemsPerPage);
        const startIndex = (page - 1) * tabConfig.itemsPerPage;
        const paginatedItems = filtered.slice(startIndex, startIndex + tabConfig.itemsPerPage);
        
        // Get the list element
        const publicationsList = document.getElementById(tabConfig.listElement);
        
        if (paginatedItems.length === 0) {
            publicationsList.innerHTML = `
                <div class="alert alert-info">
                    No ${tabId} publications found matching your criteria.
                </div>
            `;
            document.getElementById(tabConfig.paginationElement).innerHTML = '';
            return;
        }
        
        // Generate publication cards
        let html = '';
        paginatedItems.forEach(pub => {
            const pubDate = pub.publication_date ? 
                new Date(pub.publication_date).toLocaleDateString() : 
                'Date not available';
                
            const authors = pub.authors || 'Authors not specified';
            const abstract = pub.abstract ? 
                `<p class="card-text">${pub.abstract.substring(0, 300)}${pub.abstract.length > 300 ? '...' : ''}</p>` : 
                '';
            
            // Generate buttons
            let buttons = '';
            if (pub.link) {
                const isDoi = pub.link.toLowerCase().includes('doi.org/') || 
                             pub.link.startsWith('10.');
                const isPdf = pub.link.toLowerCase().endsWith('.pdf');
                
                if (isDoi) {
                    buttons += `
                        <a href="${pub.link.startsWith('http') ? '' : 'https://doi.org/'}${pub.link}" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-external-link-alt"></i> View Publication
                        </a>`;
                } else if (isPdf) {
                    buttons += `
                        <a href="${pub.link}" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>`;
                } else {
                    buttons += `
                        <a href="${pub.link}" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-external-link-alt"></i> View Details
                        </a>`;
                }
            }
            
            // Add publication category badge if available
            const categoryBadge = pub.publication_category ? 
                `<span class="badge bg-info text-dark mb-2">${pub.publication_category}</span>` : '';
            
            html += `
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title">${pub.title || 'Untitled Publication'}</h5>
                            ${categoryBadge}
                        </div>
                        <h6 class="card-subtitle mb-3 text-muted">
                            <i class="fas fa-users"></i> ${authors}
                        </h6>
                        <div class="mb-3">
                            <span class="text-muted">
                                <i class="far fa-calendar-alt"></i> ${pubDate}
                            </span>
                        </div>
                        ${abstract}
                        ${buttons}
                    </div>
                </div>
            `;
        });
        
        // Update the DOM
        publicationsList.innerHTML = html;
        renderPagination(tabId, totalItems, page);
    }
    
    // Render pagination controls
    function renderPagination(tabId, totalItems, currentPage) {
        const tabConfig = config[tabId];
        if (!tabConfig) return;
        
        const totalPages = Math.ceil(totalItems / tabConfig.itemsPerPage);
        const pagination = document.getElementById(tabConfig.paginationElement);
        
        if (totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }
        
        let html = '<nav aria-label="Page navigation"><ul class="pagination">';
        
        // Previous button
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-tab="${tabId}" data-page="${currentPage - 1}" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>`;
        
        // Page numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
        
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        // First page
        if (startPage > 1) {
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" data-tab="${tabId}" data-page="1">1</a>
                </li>`;
            if (startPage > 2) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-tab="${tabId}" data-page="${i}">${i}</a>
                </li>`;
        }
        
        // Last page
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" data-tab="${tabId}" data-page="${totalPages}">${totalPages}</a>
                </li>`;
        }
        
        // Next button
        html += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-tab="${tabId}" data-page="${currentPage + 1}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>`;
        
        html += '</ul></nav>';
        pagination.innerHTML = html;
        
        // Add event listeners to pagination links
        document.querySelectorAll(`#${tabConfig.paginationElement} .page-link[data-page]`).forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                const tabId = this.getAttribute('data-tab');
                if (page >= 1 && page <= totalPages && tabId) {
                    loadPublications(tabId, page);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        });
    }
    
    // Update last updated timestamp
    function updateLastUpdated(tabConfig, timestamp) {
        const element = document.getElementById(tabConfig.lastUpdatedElement);
        if (element) {
            const date = timestamp ? new Date(timestamp) : new Date();
            element.textContent = `Last updated: ${date.toLocaleString()}`;
        }
    }
    
    // Event listeners
    document.getElementById('filterForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        loadPublications('filtered', 1);
    });
    
    // Refresh button handlers
    document.getElementById('refreshAllBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        loadPublications('all', 1);
    });
    
    document.getElementById('refreshFilteredBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        loadPublications('filtered', 1);
    });
    
    // Initial load based on current tab
    const activeTab = document.querySelector('.tab-pane.show.active');
    if (activeTab) {
        const tabId = activeTab.id;
        loadPublications(tabId, 1);
    } else {
        // Default to all publications if no active tab found
        loadPublications('all', 1);
    }
});
</script>

<?php
require_once 'includes/footer.php';
?>

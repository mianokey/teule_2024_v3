@extends('layouts.admin')

@section('content')

<div class="container-fluid py-3">

    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-0 fw-bold text-primary">
                Child Sponsorship Cards
            </h4>

            <small class="text-muted">
                Select children for card generation
            </small>
        </div>

        <div>
            <span
                id="visibleChildCount"
                class="badge bg-secondary"
            >
                0 Children
            </span>

            <span
                id="selectedCount"
                class="badge bg-success"
            >
                0 Selected
            </span>
        </div>

    </div>


    {{-- =========================================================
        MAIN WORK AREA
    ========================================================== --}}
    <div class="row g-3">

        {{-- =====================================================
            LEFT SIDE - CARD PREVIEW
        ====================================================== --}}
        <div class="col-lg-8">

            <div class="card shadow-sm h-100">

                <div class="card-header bg-white">

                    <div class="d-flex justify-content-between align-items-center">

                        <strong>
                            Card Preview
                        </strong>

                        <div class="d-flex align-items-center gap-2">

                            <label
                                for="templateSelector"
                                class="mb-0 fw-bold"
                            >
                                Template:
                            </label>

                            <select
                                id="templateSelector"
                                class="form-select form-select-sm"
                                style="width:180px;"
                            >

                                @foreach($templates as $key => $name)

                                    <option value="{{ $key }}">
                                        {{ $name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>

                </div>


                <div
                    id="previewContainer"
                    class="card-body preview-area"
                >

                    <div
                        id="noPreviewMessage"
                        class="text-center text-muted"
                    >

                        <i class="fas fa-id-card fa-4x mb-3"></i>

                        <h5>
                            No Child Selected
                        </h5>

                        <p>
                            Select a child from the list to preview
                            their sponsorship card.
                        </p>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            RIGHT SIDE - CHILD SELECTION
        ====================================================== --}}
        <div class="col-lg-4">

            <div class="card shadow-sm selection-panel">

                <div class="card-header bg-white">

                    <strong>
                        Select Children
                    </strong>

                </div>


                <div class="card-body p-2 selection-panel-body">

                    {{-- =================================================
                        STATUS FILTER
                    ================================================== --}}
                    <div class="mb-3">

                        <label class="form-label fw-bold mb-1">
                            Filter by Status
                        </label>

                        <div class="d-flex flex-wrap gap-2">

                            @php

                                $statuses = $children
                                    ->pluck('status')
                                    ->filter()
                                    ->unique()
                                    ->sort()
                                    ->values();

                            @endphp


                            @foreach($statuses as $status)

                                <div class="form-check">

                                    <input
                                        class="form-check-input status-filter"
                                        type="checkbox"
                                        value="{{ $status }}"
                                        id="status-{{ \Illuminate\Support\Str::slug($status) }}"
                                    >

                                    <label
                                        class="form-check-label"
                                        for="status-{{ \Illuminate\Support\Str::slug($status) }}"
                                    >
                                        {{ $status }}
                                    </label>

                                </div>

                            @endforeach

                        </div>


                        <div class="mt-2">

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary"
                                onclick="selectAllStatuses()"
                            >
                                All Statuses
                            </button>

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary"
                                onclick="clearAllStatuses()"
                            >
                                Clear
                            </button>

                        </div>

                    </div>


                    {{-- =================================================
                        SEARCH
                    ================================================== --}}
                    <div class="mb-2">

                        <input
                            type="text"
                            id="childSearch"
                            class="form-control"
                            placeholder="Search child..."
                        >

                    </div>


                    {{-- =================================================
                        SELECT VISIBLE / CLEAR
                    ================================================== --}}
                    <div class="d-flex gap-2 mb-2">

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary flex-grow-1"
                            onclick="selectAllVisibleChildren()"
                        >
                            Select Visible
                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary flex-grow-1"
                            onclick="clearAllChildren()"
                        >
                            Clear Selected
                        </button>

                    </div>


                    {{-- =================================================
                        CHILD SCROLLABLE LIST
                    ================================================== --}}
                    <div
                        id="childrenSelection"
                        class="children-scroll-list"
                    >

                        @foreach($children as $child)

                            <div
                                class="child-list-item"
                                data-child-id="{{ $child->id }}"
                                data-status="{{ $child->status }}"
                                data-name="{{ strtolower($child->name) }}"
                            >

                                <div
                                    class="child-list-row"
                                    onclick="previewChild({{ $child->id }})"
                                >

                                    {{-- CHECKBOX --}}
                                    <div
                                        class="form-check"
                                        onclick="event.stopPropagation();"
                                    >

                                        <input
                                            type="checkbox"
                                            class="form-check-input child-checkbox"
                                            value="{{ $child->id }}"
                                            data-child-id="{{ $child->id }}"
                                        >

                                    </div>


                                    {{-- CHILD IMAGE --}}
                                    <div class="child-list-image">

                                        <img
                                            src="{{ asset($child->img_url) }}"
                                            alt="{{ $child->name }}"
                                        >

                                    </div>


                                    {{-- CHILD INFORMATION --}}
                                    <div class="child-list-info">

                                        <strong>
                                            {{ $child->name }}
                                        </strong>

                                        <small>
                                            {{ $child->status ?? 'No Status' }}
                                        </small>

                                    </div>


                                    {{-- PREVIEW ICON --}}
                                    <div class="ms-auto text-muted">

                                        <i class="fas fa-eye"></i>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>


                    {{-- NO CHILDREN --}}
                    <div
                        id="noChildrenMessage"
                        class="text-center text-muted py-4"
                        style="display:none;"
                    >

                        <i class="fas fa-users fa-2x mb-2"></i>

                        <div>
                            No children found.
                        </div>

                    </div>


                    {{-- =================================================
                        DOWNLOAD ALL - ALWAYS VISIBLE
                    ================================================== --}}
                    <div class="download-all-wrapper">

                        <button
                            type="button"
                            id="downloadSelectedBtn"
                            class="btn btn-success w-100"
                            onclick="downloadSelectedCards()"
                            disabled
                        >

                            <i class="fas fa-download me-2"></i>

                            Download All Selected Cards

                            <span
                                id="selectedCountBottom"
                                class="badge bg-light text-success ms-2"
                            >
                                0
                            </span>

                        </button>


                        <div
                            id="downloadHelp"
                            class="small text-muted text-center mt-2"
                        >
                            Select children using the checkboxes.
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        CARD TEMPLATE SOURCE

        One copy of each child's template is rendered here.
        These are hidden off-screen and cloned when needed.
    ========================================================== --}}
    <div
        id="cardTemplates"
        style="
            position:absolute;
            left:-100000px;
            top:0;
            width:5in;
            overflow:hidden;
        "
    >

        @foreach($children as $child)

            <div
                class="child-source"
                data-child-id="{{ $child->id }}"
            >

                @foreach($templates as $key => $name)

                    <div
                        class="source-template"
                        data-template="{{ $key }}"
                    >

                        @include(
                            'admin.children.sponscard.templates.' . $key,
                            ['child' => $child]
                        )

                    </div>

                @endforeach

            </div>

        @endforeach

    </div>

</div>


<style>

/* =========================================================
   PREVIEW AREA
========================================================= */

.preview-area {

    min-height: 760px;

    display: flex;

    align-items: center;

    justify-content: center;

    overflow: hidden;

    position: relative;
}


/*
 * Keep the actual card at its original
 * 5 x 7 inch size.
 */

.preview-area .preview-card {

    flex-shrink: 0;

}


/* Hide download button inside preview */

.preview-area .download-btn-wrapper {

    display: none !important;

}


/* =========================================================
   RIGHT SELECTION PANEL
========================================================= */

.selection-panel {

    height: calc(100vh - 120px);

    min-height: 650px;

    display: flex;

    flex-direction: column;

    position: sticky;

    top: 15px;

}


.selection-panel-body {

    display: flex;

    flex-direction: column;

    min-height: 0;

    flex: 1;

}


/* =========================================================
   CHILD LIST
========================================================= */

.children-scroll-list {

    flex: 1;

    min-height: 0;

    overflow-y: auto;

    overflow-x: hidden;

    border: 1px solid #dee2e6;

    border-radius: 6px;

    background: #f8f9fa;

}


/* =========================================================
   CHILD ROW
========================================================= */

.child-list-item {

    border-bottom: 1px solid #dee2e6;

    background: #fff;

}


.child-list-item:last-child {

    border-bottom: none;

}


.child-list-row {

    display: flex;

    align-items: center;

    gap: 8px;

    padding: 9px 10px;

    cursor: pointer;

    transition: background 0.15s ease;

}


.child-list-row:hover {

    background: #f1f8f4;

}


/* =========================================================
   SELECTED PREVIEW CHILD
========================================================= */

.child-list-item.selected-preview .child-list-row {

    background: #e8f5e9;

}


.child-list-item:has(.child-checkbox:checked) .child-list-row {

    background: #eef8f0;

}


/*
 * Preview selection should be visually stronger
 */

.child-list-item.selected-preview:has(.child-checkbox:checked)
.child-list-row {

    background: #dff3e3;

}


/* =========================================================
   CHILD IMAGE
========================================================= */

.child-list-image {

    width: 42px;

    height: 42px;

    border-radius: 50%;

    overflow: hidden;

    flex-shrink: 0;

}


.child-list-image img {

    width: 100%;

    height: 100%;

    object-fit: cover;

}


/* =========================================================
   CHILD INFO
========================================================= */

.child-list-info {

    min-width: 0;

    flex-grow: 1;

}


.child-list-info strong {

    display: block;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

    font-size: 14px;

}


.child-list-info small {

    display: block;

    color: #6c757d;

    font-size: 11px;

}


/* =========================================================
   DOWNLOAD AREA
========================================================= */

.download-all-wrapper {

    flex-shrink: 0;

    padding-top: 10px;

    background: #fff;

}


.download-all-wrapper .btn {

    padding: 10px 12px;

    font-weight: 600;

}


.download-all-wrapper .btn:disabled {

    cursor: not-allowed;

    opacity: 0.65;

}


/* =========================================================
   SCROLLBAR
========================================================= */

.children-scroll-list::-webkit-scrollbar {

    width: 7px;

}


.children-scroll-list::-webkit-scrollbar-track {

    background: #f1f1f1;

}


.children-scroll-list::-webkit-scrollbar-thumb {

    background: #aaa;

    border-radius: 10px;

}


.children-scroll-list::-webkit-scrollbar-thumb:hover {

    background: #888;

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 991px) {

    .preview-area {

        min-height: auto;

        padding: 20px 5px;

    }


    .selection-panel {

        height: auto;

        min-height: 0;

        position: static;

    }


    .selection-panel-body {

        display: block;

    }


    .children-scroll-list {

        height: 400px;

    }


    .download-all-wrapper {

        position: sticky;

        bottom: 0;

        z-index: 20;

        padding: 10px 0;

    }

}

</style>


{{-- =========================================================
    HTML2CANVAS
========================================================= --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>


<script>

/* =========================================================
   GLOBAL
========================================================= */

let currentPreviewChild = null;


/* =========================================================
   INITIALISE
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        setupStatusEvents();

        setupChildCheckboxes();

        setupSearch();

        setupTemplateSelector();

        /*
         * Start with all statuses selected.
         * This means the complete child list is visible
         * when the page first loads.
         */
        selectAllStatuses();

        updateChildrenList();

        updateSelectedCount();

    }
);


/* =========================================================
   STATUS
========================================================= */

function getSelectedStatuses()
{

    return Array.from(
        document.querySelectorAll(
            '.status-filter:checked'
        )
    ).map(function (checkbox) {

        return checkbox.value;

    });

}


function setupStatusEvents()
{

    document
        .querySelectorAll('.status-filter')
        .forEach(function (checkbox) {

            checkbox.addEventListener(
                'change',
                function () {

                    updateChildrenList();

                }
            );

        });

}


function selectAllStatuses()
{

    document
        .querySelectorAll('.status-filter')
        .forEach(function (checkbox) {

            checkbox.checked = true;

        });

    updateChildrenList();

}


function clearAllStatuses()
{

    document
        .querySelectorAll('.status-filter')
        .forEach(function (checkbox) {

            checkbox.checked = false;

        });

    updateChildrenList();

}


/* =========================================================
   CHILD FILTER
========================================================= */

function updateChildrenList()
{

    const statuses =
        getSelectedStatuses();


    const searchInput =
        document.getElementById(
            'childSearch'
        );


    const search =
        searchInput
            ? searchInput.value
                .toLowerCase()
                .trim()
            : '';


    const items =
        document.querySelectorAll(
            '.child-list-item'
        );


    let visible = 0;


    items.forEach(function (item) {

        const status =
            item.dataset.status || '';


        const name =
            item.dataset.name || '';


        /*
         * Status is the primary filter.
         */
        const statusMatch =
            statuses.length > 0 &&
            statuses.includes(status);


        /*
         * Search works within the selected statuses.
         */
        const searchMatch =
            search === '' ||
            name.includes(search);


        if (
            statusMatch &&
            searchMatch
        ) {

            item.style.display = '';

            visible++;

        }
        else {

            item.style.display = 'none';

        }

    });


    const visibleCount =
        document.getElementById(
            'visibleChildCount'
        );


    if (visibleCount) {

        visibleCount.textContent =
            visible +
            (
                visible === 1
                    ? ' Child'
                    : ' Children'
            );

    }


    const noChildrenMessage =
        document.getElementById(
            'noChildrenMessage'
        );


    if (noChildrenMessage) {

        noChildrenMessage.style.display =
            visible === 0
                ? 'block'
                : 'none';

    }

}


/* =========================================================
   SEARCH
========================================================= */

function setupSearch()
{

    const search =
        document.getElementById(
            'childSearch'
        );


    if (!search) {

        return;

    }


    search.addEventListener(
        'input',
        function () {

            updateChildrenList();

        }
    );

}


/* =========================================================
   CHECKBOXES
========================================================= */

function setupChildCheckboxes()
{

    document
        .querySelectorAll('.child-checkbox')
        .forEach(function (checkbox) {

            checkbox.addEventListener(
                'change',
                function () {

                    updateSelectedCount();

                }
            );

        });

}


/* =========================================================
   SELECT ALL VISIBLE
========================================================= */

function selectAllVisibleChildren()
{

    document
        .querySelectorAll('.child-list-item')
        .forEach(function (item) {

            if (
                item.style.display !== 'none'
            ) {

                const checkbox =
                    item.querySelector(
                        '.child-checkbox'
                    );


                if (checkbox) {

                    checkbox.checked = true;

                }

            }

        });


    updateSelectedCount();

}


/* =========================================================
   CLEAR ALL SELECTED
========================================================= */

function clearAllChildren()
{

    document
        .querySelectorAll('.child-checkbox')
        .forEach(function (checkbox) {

            checkbox.checked = false;

        });


    updateSelectedCount();

}


/* =========================================================
   GET SELECTED CHILDREN
========================================================= */

function getSelectedChildIds()
{

    return Array.from(
        document.querySelectorAll(
            '.child-checkbox:checked'
        )
    ).map(function (checkbox) {

        return checkbox.dataset.childId;

    });

}


/* =========================================================
   SELECTED COUNT
========================================================= */

function updateSelectedCount()
{

    const selected =
        getSelectedChildIds();


    const topCount =
        document.getElementById(
            'selectedCount'
        );


    if (topCount) {

        topCount.textContent =
            selected.length +
            ' Selected';

    }


    const bottomCount =
        document.getElementById(
            'selectedCountBottom'
        );


    if (bottomCount) {

        bottomCount.textContent =
            selected.length;

    }


    const button =
        document.getElementById(
            'downloadSelectedBtn'
        );


    if (button) {

        button.disabled =
            selected.length === 0;

    }


    const downloadHelp =
        document.getElementById(
            'downloadHelp'
        );


    if (downloadHelp) {

        downloadHelp.textContent =
            selected.length === 0
                ? 'Select children using the checkboxes.'
                : selected.length +
                  (
                      selected.length === 1
                          ? ' child selected for download.'
                          : ' children selected for download.'
                  );

    }

}


/* =========================================================
   TEMPLATE SELECTOR
========================================================= */

function setupTemplateSelector()
{

    const selector =
        document.getElementById(
            'templateSelector'
        );


    if (!selector) {

        return;

    }


    selector.addEventListener(
        'change',
        function () {

            if (currentPreviewChild) {

                previewChild(
                    currentPreviewChild
                );

            }

        }
    );

}


/* =========================================================
   PREVIEW CHILD
========================================================= */

function previewChild(childId)
{

    currentPreviewChild =
        childId;


    /*
     * Remove previous active preview.
     */

    document
        .querySelectorAll(
            '.child-list-item'
        )
        .forEach(function (item) {

            item.classList.remove(
                'selected-preview'
            );

        });


    /*
     * Highlight selected child.
     */

    const selectedItem =
        document.querySelector(
            '.child-list-item[data-child-id="' +
            childId +
            '"]'
        );


    if (selectedItem) {

        selectedItem.classList.add(
            'selected-preview'
        );

    }


    const previewContainer =
        document.getElementById(
            'previewContainer'
        );


    if (!previewContainer) {

        return;

    }


    /*
     * Find child's source.
     */

    const source =
        document.querySelector(
            '.child-source[data-child-id="' +
            childId +
            '"]'
        );


    if (!source) {

        showNoPreview(
            previewContainer
        );

        return;

    }


    /*
     * Get selected template.
     */

    const template =
        document.getElementById(
            'templateSelector'
        ).value;


    const sourceTemplate =
        source.querySelector(
            '.source-template[data-template="' +
            template +
            '"]'
        );


    if (!sourceTemplate) {

        console.error(
            'Template not found:',
            template
        );

        showNoPreview(
            previewContainer
        );

        return;

    }


    /*
     * Clear existing preview.
     */

    previewContainer.innerHTML = '';


    /*
     * Clone the selected template.
     */

    const preview =
        sourceTemplate.cloneNode(
            true
        );


    preview.classList.add(
        'preview-card'
    );


    preview.style.display =
        'block';


    /*
     * Hide download buttons.
     */

    preview
        .querySelectorAll(
            '.no-print'
        )
        .forEach(function (element) {

            element.style.display =
                'none';

        });


    /*
     * Add to preview.
     */

    previewContainer.appendChild(
        preview
    );

}


/* =========================================================
   NO PREVIEW
========================================================= */

function showNoPreview(container)
{

    container.innerHTML = `

        <div
            id="noPreviewMessage"
            class="text-center text-muted"
        >

            <i class="fas fa-id-card fa-4x mb-3"></i>

            <h5>
                No Child Selected
            </h5>

            <p>
                Select a child from the list to preview
                their sponsorship card.
            </p>

        </div>

    `;

}


/* =========================================================
   WAIT FOR CARD IMAGES
========================================================= */

function waitForImages(container)
{

    const images =
        Array.from(
            container.querySelectorAll('img')
        );


    if (images.length === 0) {

        return Promise.resolve();

    }


    return Promise.all(

        images.map(function (img) {

            /*
             * Already loaded.
             */

            if (img.complete) {

                return Promise.resolve();

            }


            /*
             * Wait for successful load
             * or failure.
             */

            return new Promise(function (resolve) {

                img.onload = resolve;

                img.onerror = resolve;

            });

        })

    );

}


/* =========================================================
   DOWNLOAD SELECTED CARDS
========================================================= */

async function downloadSelectedCards()
{

    const selected =
        getSelectedChildIds();


    if (selected.length === 0) {

        alert(
            'Please select at least one child.'
        );

        return;

    }


    const template =
        document.getElementById(
            'templateSelector'
        ).value;


    const button =
        document.getElementById(
            'downloadSelectedBtn'
        );


    const selectedBottom =
        document.getElementById(
            'selectedCountBottom'
        );


    const downloadHelp =
        document.getElementById(
            'downloadHelp'
        );


    const originalText =
        button.innerHTML;


    button.disabled = true;


    let completed = 0;

    let failed = 0;


    try {

        /*
         * Process each selected child individually.
         */

        for (
            let i = 0;
            i < selected.length;
            i++
        ) {

            const childId =
                selected[i];


            /*
             * Update button progress.
             */

            button.innerHTML = `

                <i class="fas fa-spinner fa-spin me-2"></i>

                Creating ${i + 1} of ${selected.length}

            `;


            if (selectedBottom) {

                selectedBottom.textContent =
                    `${i + 1}/${selected.length}`;

            }


            /*
             * Find child source.
             */

            const source =
                document.querySelector(
                    '.child-source[data-child-id="' +
                    childId +
                    '"]'
                );


            if (!source) {

                console.error(
                    'Child source not found:',
                    childId
                );

                failed++;

                continue;

            }


            /*
             * Find selected template.
             */

            const sourceTemplate =
                source.querySelector(
                    '.source-template[data-template="' +
                    template +
                    '"]'
                );


            if (!sourceTemplate) {

                console.error(
                    'Template not found:',
                    template,
                    'for child:',
                    childId
                );

                failed++;

                continue;

            }


            /*
             * Clone the complete template.
             */

            const wrapper =
                sourceTemplate.cloneNode(
                    true
                );


            /*
             * IMPORTANT:
             * Position the card off-screen but
             * keep it rendered so html2canvas
             * can capture it correctly.
             */

            wrapper.style.position =
                'fixed';

            wrapper.style.left =
                '-10000px';

            wrapper.style.top =
                '0';

            wrapper.style.width =
                '5in';

            wrapper.style.height =
                '7in';

            wrapper.style.display =
                'block';

            wrapper.style.visibility =
                'visible';

            wrapper.style.opacity =
                '1';

            wrapper.style.zIndex =
                '-9999';


            document.body.appendChild(
                wrapper
            );


            /*
             * Find actual card.
             */

            const card =
                wrapper.querySelector(
                    '.child-card'
                );


            if (!card) {

                console.error(
                    'No .child-card found for:',
                    childId
                );

                wrapper.remove();

                failed++;

                continue;

            }


            /*
             * Make sure card has its
             * original dimensions.
             */

            card.style.width =
                '5in';

            card.style.height =
                '7in';


            /*
             * Hide download buttons.
             */

            wrapper
                .querySelectorAll(
                    '.no-print'
                )
                .forEach(function (element) {

                    element.style.display =
                        'none';

                });


            /*
             * Wait for all images.
             */

            await waitForImages(wrapper);


            /*
             * Give the browser time to render
             * background image, QR and child image.
             */

            await new Promise(function (resolve) {

                setTimeout(
                    resolve,
                    500
                );

            });


            /*
             * Generate PNG.
             */

            const canvas =
                await html2canvas(
                    card,
                    {
                        scale: 3,

                        useCORS: true,

                        allowTaint: false,

                        backgroundColor: null,

                        logging: false,

                        imageTimeout: 15000,

                        removeContainer: true
                    }
                );


            /*
             * Convert to PNG.
             */

            const image =
                canvas.toDataURL(
                    'image/png'
                );


            /*
             * Create download link.
             */

            const link =
                document.createElement(
                    'a'
                );


            link.href =
                image;


            link.download =
                'child_card_' +
                childId +
                '.png';


            /*
             * Trigger download.
             */

            document.body.appendChild(
                link
            );


            link.click();


            link.remove();


            /*
             * Remove temporary card.
             */

            wrapper.remove();


            completed++;


            /*
             * Give browser time to
             * process download.
             */

            await new Promise(function (resolve) {

                setTimeout(
                    resolve,
                    700
                );

            });

        }


        /*
         * Finished.
         */

        button.innerHTML = `

            <i class="fas fa-check me-2"></i>

            ${completed} Card${completed === 1 ? '' : 's'} Downloaded

        `;


        if (failed > 0) {

            downloadHelp.textContent =
                completed +
                ' downloaded, ' +
                failed +
                ' failed.';

        }
        else {

            downloadHelp.textContent =
                'All selected cards have been downloaded.';

        }


        /*
         * Restore button after a short delay.
         */

        setTimeout(function () {

            button.innerHTML =
                originalText;

            updateSelectedCount();

        }, 3000);

    }
    catch (error) {

        console.error(
            'Card generation error:',
            error
        );


        alert(
            'There was a problem generating the cards. ' +
            'Please check the browser console for details.'
        );


        button.innerHTML =
            originalText;


        updateSelectedCount();

    }


    button.disabled =
        getSelectedChildIds().length === 0;

}

</script>

@endsection
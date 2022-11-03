<script>
    import { paginate, LightPaginationNav } from "svelte-paginate";
    import { onMount } from "svelte/internal";

    let items = [];
    let currentPage = 1;
    let pageSize = 10;
    $: paginatedItems = paginate({ items, pageSize, currentPage });

    function getStudents(page = 1) {
        axios.get("/api/students?page=" + currentPage).then((response) => {
            items = response.data.data;
        });
    }

    onMount(() => {
        getStudents();
    });
</script>

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Laravel/Svelte DataTable Tutorial</h3>
        </div>
    </div>
    <div class="d-flex justify-content-between align-content-center mb-2">
        <div class="d-flex">
            <div>
                <div class="d-flex align-items-center ml-4">
                    <label for="paginate" class="text-nowrap mr-2 mb-0">
                        Per Page
                    </label>
                    <select class="form-control form-control-sm">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                    </select>
                </div>
            </div>
            <div>
                <div class="d-flex align-items-center ml-4">
                    <label for="paginate" class="text-nowrap mr-2 mb-0"
                        >FilterBy Class</label
                    >
                    <select class="form-control form-control-sm">
                        <option value="">All Class</option>
                        <option value="1">Class 1</option>
                    </select>
                </div>
            </div>
            <div>
                <div class="d-flex align-items-center ml-4">
                    <label for="paginate" class="text-nowrap mr-2 mb-0"
                        >Section</label
                    >
                    <select class="form-control form-control-sm">
                        <option value="">Select a Section</option>
                        <option value="1">Section A</option>
                    </select>
                </div>
            </div>
            <div>
                <div class="dropdown ml-4">
                    <button
                        class="btn btn-secondary dropdown-toggle"
                        data-toggle="dropdown"
                    >
                        With Checked (1)
                    </button>
                    <div class="dropdown-menu">
                        <a href="#" class="dropdown-item" type="button">
                            Delete
                        </a>
                        <a class="dropdown-item" type="button"> Export </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <input
                type="search"
                class="form-control"
                placeholder="Search by name,email,phone,or address..."
            />
        </div>
    </div>
    <div class="col-md-10 mt-3 mb-3">
        <div>
            You are currently selecting all <strong>10</strong> items.
        </div>
        <div>
            You have selected <strong>10</strong> items, Do you want to Select
            All <strong>25</strong> items?
            <a href="#" class="ml-2">Select All</a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <tbody>
                <tr>
                    <th><input type="checkbox" /></th>
                    <th> Student's Name </th>
                    <th> Email </th>
                    <th> Address </th>
                    <th> Phone Number </th>
                    <th> Created At </th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Action</th>
                </tr>

                {#each paginatedItems as student}
                    <tr>
                        <td>
                            <input type="checkbox" />
                        </td>
                        <td>{student.name}</td>
                        <td>{student.email}</td>
                        <td>{student.address}</td>
                        <td>{student.phone_number}</td>
                        <td>{student.created_at}</td>
                        <td>{student.class}</td>
                        <td>{student.section}</td>
                        <td>
                            <button class="btn btn-danger btn-sm">
                                <i class="fa fa-trash" aria-hidden="true" />
                            </button>
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>
    <div class="row mt-4">
        <div class="col-sm-6 offset-5">
            <LightPaginationNav
                totalItems={items.length}
                {pageSize}
                {currentPage}
                limit={1}
                showStepOptions={true}
                on:setPage={(e) => (currentPage = e.detail.page)}
            />
        </div>
    </div>
</div>

<script>
    import { onMount } from "svelte/internal";
    import { SvelteToast, toast } from "@zerodevx/svelte-toast";

    // pagination related items
    let items = [];
    let currentPage = 1;
    let pageSize = 10;

    let searchTerm = "",
        links = [],
        meta = [],
        selectedClass = "",
        sort_direction = "asc",
        sort_field = "name",
        selectedSection = "",
        selectPage = false,
        selectAll = false;

    let classes = [],
        sections = [],
        checked = [];

    $: studentsUrl = `/api/students?page=${currentPage}&q=${searchTerm}&selectedClass=${selectedClass}&selectedSection=${selectedSection}&sort_direction=${sort_direction}&sort_field=${sort_field}&paginate=${pageSize}`;
    $: studentsUrlWithoutPagination = `/api/students?q=${searchTerm}&selectedClass=${selectedClass}&selectedSection=${selectedSection}&sort_direction=${sort_direction}&sort_field=${sort_field}`;
    $: studentsUrl, getStudents();

    $: selectPageUpdated(selectPage);

    $: selectedClass,
        (function () {
            if (selectedClass) {
                selectedSection = "";
                axios
                    .get("/api/sections?class_id=" + selectedClass)
                    .then((response) => {
                        sections = response.data.data;
                    });
            }
        })();

    function getStudents() {
        axios.get(studentsUrl).then((response) => {
            items = response.data.data;
            meta = response.data.meta;
            links = meta.links;
        });
    }

    function selectPageUpdated(selectPage) {
        if (!selectPage) {
            checked = [];
            // fix this bug later
            selectAll = false;
            // selectPage = false;
        }

        if (selectPage) {
            // fix this later
            checked = [];
            items.forEach((item) => {
                checked = [...checked, item.id];
            });
        }
    }

    function getClasses() {
        axios.get("/api/classes").then((response) => {
            classes = response.data.data;
        });
    }

    function deleteSingleRecord(student_id) {
        axios.delete(`/api/student/${student_id}/delete/`).then((response) => {
            checked = checked.filter((id) => id != student_id);
            toast.push("Student record deleted successfully");
            getStudents();
        });
    }

    function deleteRecords() {
        axios
            .delete(`/api/students/${checked}/massDestroy/`)
            .then((response) => {
                if (response.status == 204) {
                    toast.push("Selected Students were Deleted Successfully");
                    checked = [];
                    getStudents();
                }
            });
    }

    function change_sort(field) {
        if (sort_field == field) {
            sort_direction = sort_direction == "asc" ? "desc" : "asc";
        } else {
            sort_field = field;
        }
    }

    function selectAllRecords() {
        axios.get(studentsUrlWithoutPagination).then((response) => {
            checked = [];
            response.data.data.forEach((student) => {
                checked = [...checked, student.id];
                selectAll = true;
            });
        });
    }

    function updatePagination(url) {
        currentPage = url.split("=")[1];
    }

    onMount(() => {
        getStudents();
        getClasses();
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
            <!-- pagination -->
            <div>
                <div class="d-flex align-items-center ml-4">
                    <label for="paginate" class="text-nowrap mr-2 mb-0">
                        Per Page
                    </label>
                    <select
                        bind:value={pageSize}
                        class="form-control form-control-sm"
                    >
                        <option>10</option>
                        <option>20</option>
                        <option>30</option>
                    </select>
                </div>
            </div>

            <!-- filter by class -->
            <div>
                <div class="d-flex align-items-center ml-4">
                    <label for="paginate" class="text-nowrap mr-2 mb-0">
                        FilterBy Class
                    </label>
                    <select
                        class="form-control form-control-sm"
                        bind:value={selectedClass}
                    >
                        <option value="" selected>All Class</option>
                        {#each classes as item}
                            <option value={item.id}>{item.name}</option>
                        {/each}
                    </select>
                </div>
            </div>

            {#if selectedClass}
                <!-- filter by section -->
                <div>
                    <div class="d-flex align-items-center ml-4">
                        <label for="paginate" class="text-nowrap mr-2 mb-0">
                            Section
                        </label>
                        <select
                            bind:value={selectedSection}
                            class="form-control form-control-sm"
                        >
                            <option value="">Select a Section</option>
                            {#each sections as section}
                                <option value={section.id}>
                                    {section.name}
                                </option>
                            {/each}
                        </select>
                    </div>
                </div>
            {/if}

            <!-- checkbox -->
            {#if checked.length > 0}
                <div>
                    <div class="dropdown ml-4">
                        <button
                            class="btn btn-secondary dropdown-toggle"
                            data-toggle="dropdown"
                        >
                            With Checked ({checked.length})
                        </button>
                        <div class="dropdown-menu">
                            <button
                                on:click={() =>
                                    confirm(
                                        "Are you sure you wanna delete this Record?"
                                    ) || event.stopImmediatePropagation()}
                                class="dropdown-item"
                                type="button"
                                on:click={deleteRecords}
                            >
                                Delete
                            </button>
                            <a
                                href={`api/students/${checked}/export`}
                                class="dropdown-item"
                                type="button"
                            >
                                Export
                            </a>
                        </div>
                    </div>
                </div>
            {/if}
        </div>

        <!-- search -->
        <div class="col-md-4">
            <input
                bind:value={searchTerm}
                type="search"
                class="form-control"
                placeholder="Search by name,email,phone,or address..."
            />
        </div>
    </div>

    <!-- currently selected info section -->
    <div class="col-md-10 mt-3 mb-3">
        <div>
            <!-- fix the OR condition check later -->
            {#if selectAll || meta.total == checked.length}
                You are currently selecting all <strong>{meta.total}</strong> items.
            {:else if selectPage}
                You have selected <strong>{checked.length}</strong> items, Do
                you want to Select All <strong>{meta.total}</strong> items?
                <a
                    on:click|preventDefault={selectAllRecords}
                    href="#"
                    class="ml-2">Select All</a
                >
            {/if}
        </div>
    </div>

    <!-- table -->
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <tbody>
                <tr class={selectPage || selectAll ? "table-primary" : ""}>
                    <th><input type="checkbox" bind:checked={selectPage} /></th>
                    <th>
                        <a
                            href="#"
                            on:click|preventDefault={() => change_sort("name")}
                        >
                            Student's Name
                        </a>
                        {#if sort_direction == "desc" && sort_field == "name"}
                            <span>&uarr;</span>
                        {:else if sort_direction == "asc" && sort_field == "name"}
                            <span>&darr;</span>
                        {/if}
                    </th>
                    <th>
                        <a
                            href="#"
                            on:click|preventDefault={() => change_sort("email")}
                        >
                            Email
                        </a>
                        {#if sort_direction == "desc" && sort_field == "email"}
                            <span>&uarr;</span>
                        {:else if sort_direction == "asc" && sort_field == "email"}
                            <span>&darr;</span>
                        {/if}
                    </th>
                    <th>
                        <a
                            href="#"
                            on:click|preventDefault={() =>
                                change_sort("address")}
                        >
                            Address
                        </a>
                        {#if sort_direction == "desc" && sort_field == "address"}
                            <span>&uarr;</span>
                        {:else if sort_direction == "asc" && sort_field == "address"}
                            <span>&darr;</span>
                        {/if}
                    </th>
                    <th>
                        <a
                            href="#"
                            on:click|preventDefault={() =>
                                change_sort("phone_number")}
                        >
                            Phone Number
                        </a>
                        {#if sort_direction == "desc" && sort_field == "phone_number"}
                            <span>&uarr;</span>
                        {:else if sort_direction == "asc" && sort_field == "phone_number"}
                            <span>&darr;</span>
                        {/if}
                    </th>
                    <th>
                        <a
                            href="#"
                            on:click|preventDefault={() =>
                                change_sort("created_at")}
                        >
                            Created At
                        </a>
                        {#if sort_direction == "desc" && sort_field == "created_at"}
                            <span>&uarr;</span>
                        {:else if sort_direction == "asc" && sort_field == "created_at"}
                            <span>&darr;</span>
                        {/if}
                    </th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Action</th>
                </tr>

                {#each items as student, i (student.id)}
                    <tr
                        class={checked.includes(student.id)
                            ? "table-primary"
                            : ""}
                    >
                        <td>
                            <input
                                type="checkbox"
                                value={student.id}
                                bind:group={checked}
                            />
                        </td>
                        <td>{student.name}</td>
                        <td>{student.email}</td>
                        <td>{student.address}</td>
                        <td>{student.phone_number}</td>
                        <td>{student.created_at}</td>
                        <td>{student.class}</td>
                        <td>{student.section}</td>
                        <td>
                            <button
                                on:click={() => {
                                    confirm(
                                        "Are you sure you wanna delete this Record?"
                                    ) || event.stopImmediatePropagation();
                                }}
                                class="btn btn-danger btn-sm"
                                on:click={deleteSingleRecord(student.id)}
                            >
                                <i class="fa fa-trash" aria-hidden="true" />
                            </button>
                        </td>
                    </tr>
                {:else}
                    <tr>
                        <td colspan="12">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>

    <!-- pagination -->
    <nav aria-label="Page navigation example" class="my-4 mx-5">
        <ul class="pagination justify-content-end">
            {#each links as link}
                <li
                    class="page-item {link.active || !link.url
                        ? 'disabled'
                        : ''}"
                >
                    <a
                        class="page-link"
                        href="#"
                        on:click|preventDefault={() =>
                            updatePagination(link.url)}
                    >
                        {@html link.label}
                    </a>
                </li>
            {/each}
        </ul>
    </nav>
</div>

<SvelteToast />

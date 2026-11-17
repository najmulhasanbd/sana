@extends('admin.layouts.master')
@section('title', 'ড্যাশবোর্ড')
@section('content')
      <div class="container-fluid p-4">
        <div class="row g-3 mb-4">
          <div class="col-xxl-3 col-xl-4 col-md-6 col-sm-6 stagger-anim delay-1">
            <div class="single_card stat-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 text-muted fw-semibold" style="font-size: 0.85rem;">Website Revenue</p>
                <div
                  class="icon-wrapper card-icon-purple rounded-circle d-flex justify-content-center align-items-center"
                  style="width: 35px; height: 35px;">
                  <i class="fa-solid fa-cart-shopping fs-6"></i>
                </div>
              </div>
              <div class="d-flex align-items-baseline gap-2">
                <h3 class="mb-0 fw-bold" style="color: #1e293b;">৳ 689</h3>
                <span class="trend-badge positive py-1 px-2" style="font-size: 0.7rem;"><i
                    class="fa-solid fa-arrow-trend-up"></i> +12%</span>
              </div>
              <div class="footer-text mt-3 text-muted" style="font-size: 0.75rem;">Website Orders : 0</div>
            </div>
          </div>

          <div class="col-xxl-3 col-xl-4 col-md-6 col-sm-6 stagger-anim delay-2">
            <div class="single_card stat-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 text-muted fw-semibold" style="font-size: 0.85rem;">POS Revenue</p>
                <div class="icon-wrapper card-icon-blue rounded-circle d-flex justify-content-center align-items-center"
                  style="width: 35px; height: 35px;">
                  <i class="fa-solid fa-calculator fs-6"></i>
                </div>
              </div>
              <div class="d-flex align-items-baseline gap-2">
                <h3 class="mb-0 fw-bold" style="color: #1e293b;">৳ 2,450</h3>
                <span class="trend-badge positive py-1 px-2" style="font-size: 0.7rem;"><i
                    class="fa-solid fa-arrow-trend-up"></i> +8%</span>
              </div>
              <div class="footer-text mt-3 text-muted" style="font-size: 0.75rem;">POS Orders : 12</div>
            </div>
          </div>

          <div class="col-xxl-3 col-xl-4 col-md-6 col-sm-6 stagger-anim delay-3">
            <div class="single_card stat-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 text-muted fw-semibold" style="font-size: 0.85rem;">Total Revenue</p>
                <div class="icon-wrapper card-icon-cyan rounded-circle d-flex justify-content-center align-items-center"
                  style="width: 35px; height: 35px;">
                  <i class="fa-solid fa-truck fs-6"></i>
                </div>
              </div>
              <div class="d-flex align-items-baseline gap-2">
                <h3 class="mb-0 fw-bold" style="color: #1e293b;">৳ 3,139</h3>
              </div>
              <div class="footer-text mt-3 text-muted" style="font-size: 0.75rem;">Shipping fees excluded</div>
            </div>
          </div>

          <div class="col-xxl-3 col-xl-4 col-md-6 col-sm-6">
            <div class="single_card stat-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 text-muted fw-semibold" style="font-size: 0.85rem;">Low Stocks</p>
                <div class="icon-wrapper card-icon-pink rounded-circle d-flex justify-content-center align-items-center"
                  style="width: 35px; height: 35px;">
                  <i class="fa-solid fa-triangle-exclamation fs-6"></i>
                </div>
              </div>
              <div class="d-flex align-items-baseline gap-2">
                <h3 class="mb-0 fw-bold" style="color: #1e293b;">8 Items</h3>
                <span class="alert-tag py-1 px-2" style="font-size: 0.7rem;">Alert</span>
              </div>
              <div class="footer-text mt-3 text-danger" style="font-size: 0.75rem;">Requires immediate attention</div>
            </div>
          </div>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
          <div class="col">
            <div class="small-stat-card d-flex flex-column" style="border-left-color: #6366f1">
              <div class="d-flex align-items-center w-100 mb-2">
                <div class="icon-box icon-purple">
                  <i class="fa-solid fa-users"></i>
                </div>
                <div class="info ms-2">
                  <p>Users Active</p>
                  <h4>6</h4>
                </div>
              </div>
              <div class="w-100 mt-2">
                <div class="progress" style="height: 4px;">
                  <div class="progress-bar" role="progressbar" style="width: 75%; background-color: #6366f1;"
                    aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="small-stat-card d-flex flex-column" style="border-left-color: #10b981">
              <div class="d-flex align-items-center w-100 mb-2">
                <div class="icon-box icon-success">
                  <i class="fa-solid fa-user-plus"></i>
                </div>
                <div class="info ms-2">
                  <p>New Customers</p>
                  <h4>12</h4>
                </div>
              </div>
              <div class="w-100 mt-2">
                <div class="progress" style="height: 4px;">
                  <div class="progress-bar" role="progressbar" style="width: 45%; background-color: #10b981;"
                    aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="small-stat-card d-flex flex-column" style="border-left-color: #f59e0b">
              <div class="d-flex align-items-center w-100 mb-2">
                <div class="icon-box icon-warning">
                  <i class="fa-solid fa-user-tie"></i>
                </div>
                <div class="info ms-2">
                  <p>Staff Online</p>
                  <h4>4</h4>
                </div>
              </div>
              <div class="w-100 mt-2">
                <div class="progress" style="height: 4px;">
                  <div class="progress-bar" role="progressbar" style="width: 20%; background-color: #f59e0b;"
                    aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="small-stat-card d-flex flex-column" style="border-left-color: #0ea5e9">
              <div class="d-flex align-items-center w-100 mb-2">
                <div class="icon-box icon-info">
                  <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="info ms-2">
                  <p>Messages</p>
                  <h4>3</h4>
                </div>
              </div>
              <div class="w-100 mt-2">
                <div class="progress" style="height: 4px;">
                  <div class="progress-bar" role="progressbar" style="width: 15%; background-color: #0ea5e9;"
                    aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row mt-3 g-3">
          <div class="col-xl-4 col-md-12">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold m-0 text-dark">Sales By Category</h6>
              </div>
              <div class="position-relative mb-4" style="height: 250px">
                <canvas id="earningsChart"></canvas>
              </div>
              <div class="row g-3">
                <div class="col-6 d-flex align-items-center small text-muted">
                  <span class="badge rounded-circle me-2"
                    style="background: #3c407a; width: 8px; height: 8px; padding: 0;"></span> Sales
                </div>
                <div class="col-6 d-flex align-items-center small text-muted">
                  <span class="badge rounded-circle me-2"
                    style="background: #BDCFFB; width: 8px; height: 8px; padding: 0;"></span> Pending
                </div>
                <div class="col-6 d-flex align-items-center small text-muted">
                  <span class="badge rounded-circle me-2"
                    style="background: #9B8DFB; width: 8px; height: 8px; padding: 0;"></span> Utilities
                </div>
                <div class="col-6 d-flex align-items-center small text-muted">
                  <span class="badge rounded-circle me-2"
                    style="background: #FFA412; width: 8px; height: 8px; padding: 0;"></span> Delivery
                </div>
              </div>
            </div>
          </div>

          <!-- Massive Area Chart (Monthly Sales) -->
          <div class="col-xl-8 col-md-12">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                  <h6 class="fw-bold m-0 text-dark">Monthly Sales Statistics</h6>
                </div>
                <div class="d-flex align-items-center gap-3">
                  <div class="d-none d-sm-flex align-items-center gap-3 small text-muted">
                    <span><i class="fa-solid fa-circle me-1" style="color: #3c407a; font-size: 6px"></i> Offline</span>
                    <span><i class="fa-solid fa-circle me-1" style="color: #ffa412; font-size: 6px"></i> Online</span>
                  </div>
                </div>
              </div>
              <div class="position-relative" style="height: 350px">
                <canvas id="ordersChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Products and To-Do List Section -->
        <div class="row mt-4 g-3">
          <!-- Top Selling Products -->
          <div class="col-xl-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0" style="color: #2b3674;">Top Selling Products</h5>
                <button class="btn btn-light btn-sm px-3 rounded-3" style="color: #6366f1; font-weight: 500;">See
                  All</button>
              </div>
              <div class="d-flex flex-column gap-3">
                <!-- Product 1 -->
                <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-white"
                  style="border-color: #f1f5f9 !important;">
                  <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                      style="width: 50px; height: 50px; background-color: #f8fafc;">
                      <i class="fa-solid fa-laptop text-primary fs-4"></i>
                    </div>
                    <div>
                      <h6 class="fw-bold mb-1" style="color: #2b3674;">MacBook Pro M2</h6>
                      <small class="text-muted">Electronics</small>
                    </div>
                  </div>
                  <div class="text-end">
                    <h6 class="fw-bold mb-1" style="color: #2b3674;">৳ 145,000</h6>
                    <small class="text-success fw-bold">124 Sales</small>
                  </div>
                </div>
                <!-- Product 2 -->
                <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-white"
                  style="border-color: #f1f5f9 !important;">
                  <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                      style="width: 50px; height: 50px; background-color: #f8fafc;">
                      <i class="fa-solid fa-mobile-screen text-info fs-4"></i>
                    </div>
                    <div>
                      <h6 class="fw-bold mb-1" style="color: #2b3674;">iPhone 14 Pro Max</h6>
                      <small class="text-muted">Smartphones</small>
                    </div>
                  </div>
                  <div class="text-end">
                    <h6 class="fw-bold mb-1" style="color: #2b3674;">৳ 132,000</h6>
                    <small class="text-success fw-bold">98 Sales</small>
                  </div>
                </div>
                <!-- Product 3 -->
                <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-white"
                  style="border-color: #f1f5f9 !important;">
                  <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                      style="width: 50px; height: 50px; background-color: #f8fafc;">
                      <i class="fa-solid fa-headphones text-pink fs-4"></i>
                    </div>
                    <div>
                      <h6 class="fw-bold mb-1" style="color: #2b3674;">AirPods Pro 2</h6>
                      <small class="text-muted">Accessories</small>
                    </div>
                  </div>
                  <div class="text-end">
                    <h6 class="fw-bold mb-1" style="color: #2b3674;">৳ 28,500</h6>
                    <small class="text-success fw-bold">210 Sales</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- To-Do List -->
          <div class="col-xl-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0" style="color: #2b3674;">Tasks</h5>
                <button class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center"
                  style="width: 32px; height: 32px; background-color: #6366f1; border: none;">
                  <i class="fa-solid fa-plus"></i>
                </button>
              </div>
              <div class="d-flex flex-column gap-3 todo-list">
                <div
                  class="form-check custom-checkbox d-flex justify-content-between align-items-center border-bottom pb-3 mb-0">
                  <div>
                    <input class="form-check-input me-3" type="checkbox" value="" id="task1"
                      style="width: 20px; height: 20px; cursor: pointer;">
                    <label class="form-check-label fw-bold text-dark cursor-pointer" for="task1">
                      Review Server Logs
                    </label>
                  </div>
                  <span class="badge bg-danger rounded-pill px-3 py-2">High</span>
                </div>

                <div
                  class="form-check custom-checkbox d-flex justify-content-between align-items-center border-bottom pb-3 mb-0">
                  <div>
                    <input class="form-check-input me-3" type="checkbox" value="" id="task2" checked
                      style="width: 20px; height: 20px; cursor: pointer;">
                    <label class="form-check-label fw-bold text-muted text-decoration-line-through cursor-pointer"
                      for="task2">
                      Update User Permissions
                    </label>
                  </div>
                  <span class="badge bg-warning rounded-pill px-3 py-2 text-dark">Medium</span>
                </div>

                <div
                  class="form-check custom-checkbox d-flex justify-content-between align-items-center border-bottom pb-3 mb-0">
                  <div>
                    <input class="form-check-input me-3" type="checkbox" value="" id="task3"
                      style="width: 20px; height: 20px; cursor: pointer;">
                    <label class="form-check-label fw-bold text-dark cursor-pointer" for="task3">
                      Reply to Support Tickets
                    </label>
                  </div>
                  <span class="badge bg-success rounded-pill px-3 py-2">Low</span>
                </div>

                <div class="form-check custom-checkbox d-flex justify-content-between align-items-center mb-0">
                  <div>
                    <input class="form-check-input me-3" type="checkbox" value="" id="task4"
                      style="width: 20px; height: 20px; cursor: pointer;">
                    <label class="form-check-label fw-bold text-dark cursor-pointer" for="task4">
                      Schedule Backup
                    </label>
                  </div>
                  <span class="badge bg-danger rounded-pill px-3 py-2">High</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Orders and Timeline Section -->
        <div class="row mt-4 g-3">
          <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0" style="color: #2b3674;">Recent Orders</h5>
                <div class="d-flex gap-2">
                  <div class="position-relative">
                    <i class="fa-solid fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"
                      style="font-size: 0.8rem;"></i>
                    <input type="text" class="form-control form-control-sm ps-5 border-0 bg-light"
                      placeholder="Search orders..." style="border-radius: 8px; height: 35px;">
                  </div>
                  <button class="btn btn-light btn-sm px-3 d-flex align-items-center gap-2"
                    style="border-radius: 8px; color: #475569; border: 1px solid #e2e8f0;">
                    <i class="fa-solid fa-filter"></i> Filter
                  </button>
                  <button class="btn btn-primary btn-sm px-3 d-flex align-items-center gap-2"
                    style="border-radius: 8px; background-color: #6366f1; border: none;">
                    <i class="fa-solid fa-cloud-arrow-down"></i> Export
                  </button>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-hover align-middle custom-table mb-0">
                  <thead>
                    <tr>
                      <th style="width: 40px;">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" value="" id="selectAll">
                        </div>
                      </th>
                      <th class="text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Order
                        ID</th>
                      <th class="text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        Customer</th>
                      <th class="text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Date
                      </th>
                      <th class="text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        Status</th>
                      <th class="text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        Amount</th>
                      <th class="text-secondary text-uppercase text-end"
                        style="font-size: 0.75rem; letter-spacing: 0.5px;">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" value="">
                        </div>
                      </td>
                      <td class="fw-bold" style="color: #2b3674;">#ORD-0012</td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="rounded-circle d-flex align-items-center justify-content-center me-2"
                            style="width: 35px; height: 35px; background-color: rgba(99, 102, 241, 0.1); color: #6366f1; font-weight: bold;">
                            AK</div>
                          <span style="font-weight: 500; color: #2b3674;">Anisur Khan</span>
                        </div>
                      </td>
                      <td style="color: #8f9bba; font-size: 0.9rem;">Oct 12, 2024</td>
                      <td><span class="badge"
                          style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 6px 12px; border-radius: 20px;">Completed</span>
                      </td>
                      <td class="fw-bold" style="color: #2b3674;">৳ 1,450</td>
                      <td class="text-end">
                        <button class="btn btn-sm btn-light rounded-circle" style="width: 32px; height: 32px;"><i
                            class="fa-solid fa-ellipsis-vertical text-muted"></i></button>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" value="">
                        </div>
                      </td>
                      <td class="fw-bold" style="color: #2b3674;">#ORD-0013</td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="rounded-circle d-flex align-items-center justify-content-center me-2"
                            style="width: 35px; height: 35px; background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; font-weight: bold;">
                            RR</div>
                          <span style="font-weight: 500; color: #2b3674;">Rahim Rahman</span>
                        </div>
                      </td>
                      <td style="color: #8f9bba; font-size: 0.9rem;">Oct 12, 2024</td>
                      <td><span class="badge"
                          style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 6px 12px; border-radius: 20px;">Pending</span>
                      </td>
                      <td class="fw-bold" style="color: #2b3674;">৳ 850</td>
                      <td class="text-end">
                        <button class="btn btn-sm btn-light rounded-circle" style="width: 32px; height: 32px;"><i
                            class="fa-solid fa-ellipsis-vertical text-muted"></i></button>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" value="">
                        </div>
                      </td>
                      <td class="fw-bold" style="color: #2b3674;">#ORD-0014</td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="rounded-circle d-flex align-items-center justify-content-center me-2"
                            style="width: 35px; height: 35px; background-color: rgba(239, 68, 68, 0.1); color: #ef4444; font-weight: bold;">
                            SH</div>
                          <span style="font-weight: 500; color: #2b3674;">Salma Hossain</span>
                        </div>
                      </td>
                      <td style="color: #8f9bba; font-size: 0.9rem;">Oct 11, 2024</td>
                      <td><span class="badge"
                          style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 6px 12px; border-radius: 20px;">Cancelled</span>
                      </td>
                      <td class="fw-bold" style="color: #2b3674;">৳ 2,100</td>
                      <td class="text-end">
                        <button class="btn btn-sm btn-light rounded-circle" style="width: 32px; height: 32px;"><i
                            class="fa-solid fa-ellipsis-vertical text-muted"></i></button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Activity Timeline Widget -->
          <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0" style="color: #2b3674;">Activity Timeline</h5>
                <i class="fa-solid fa-ellipsis text-muted cursor-pointer"></i>
              </div>
              <div class="activity-timeline">
                <div class="timeline-item d-flex position-relative mb-4">
                  <div
                    class="timeline-badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                    style="width: 30px; height: 30px; z-index: 1;">
                    <i class="fa-solid fa-cart-shopping fs-7"></i>
                  </div>
                  <div class="timeline-content pb-3 border-bottom w-100">
                    <h6 class="fw-bold mb-1" style="color: #2b3674;">New Order Placed</h6>
                    <p class="text-muted small mb-0">Order #ORD-0012 was placed by Anisur Khan</p>
                    <span class="text-muted" style="font-size: 0.75rem;">12 mins ago</span>
                  </div>
                </div>

                <div class="timeline-item d-flex position-relative mb-4">
                  <div
                    class="timeline-badge bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                    style="width: 30px; height: 30px; z-index: 1;">
                    <i class="fa-solid fa-check fs-7"></i>
                  </div>
                  <div class="timeline-content pb-3 border-bottom w-100">
                    <h6 class="fw-bold mb-1" style="color: #2b3674;">Payment Completed</h6>
                    <p class="text-muted small mb-0">Payment of ৳ 1,450 received successfully.</p>
                    <span class="text-muted" style="font-size: 0.75rem;">1 hour ago</span>
                  </div>
                </div>

                <div class="timeline-item d-flex position-relative mb-4">
                  <div
                    class="timeline-badge bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                    style="width: 30px; height: 30px; z-index: 1;">
                    <i class="fa-solid fa-user-plus fs-7"></i>
                  </div>
                  <div class="timeline-content pb-3 border-bottom w-100">
                    <h6 class="fw-bold mb-1" style="color: #2b3674;">New User Registered</h6>
                    <p class="text-muted small mb-0">Rahim Rahman created a new account.</p>
                    <span class="text-muted" style="font-size: 0.75rem;">3 hours ago</span>
                  </div>
                </div>

                <div class="timeline-item d-flex position-relative mb-0">
                  <div
                    class="timeline-badge bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                    style="width: 30px; height: 30px; z-index: 1;">
                    <i class="fa-solid fa-server fs-7"></i>
                  </div>
                  <div class="timeline-content w-100">
                    <h6 class="fw-bold mb-1" style="color: #2b3674;">Server Restarted</h6>
                    <p class="text-muted small mb-0">Routine maintenance performed.</p>
                    <span class="text-muted" style="font-size: 0.75rem;">1 day ago</span>
                  </div>
                </div>
              </div>
              <button class="btn btn-light w-100 mt-3 text-primary fw-bold" style="border-radius: 8px;">View All
                Activity</button>
            </div>
          </div>
        </div>

        <!-- Support Tickets and System Health Section -->
        <div class="row mt-4 g-3">
          <!-- Support Tickets -->
          <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0" style="color: #2b3674;">Support Tickets</h5>
                <button class="btn btn-light btn-sm px-3 rounded-3" style="color: #6366f1; font-weight: 500;">View
                  All</button>
              </div>
              <div class="d-flex flex-column gap-3">
                <!-- Ticket 1 -->
                <div class="d-flex align-items-start gap-3 p-3 border rounded-3 bg-white"
                  style="border-color: #f1f5f9 !important;">
                  <img src="https://i.pravatar.cc/150?u=1" class="rounded-circle"
                    style="width: 40px; height: 40px; object-fit: cover;" alt="User">
                  <div class="w-100">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <h6 class="fw-bold m-0" style="color: #2b3674;">Cannot upload avatar</h6>
                      <span class="badge bg-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">In
                        Progress</span>
                    </div>
                    <p class="text-muted small mb-1">I am getting an error code 500 when trying to upload a PNG file.
                    </p>
                    <small class="text-muted" style="font-size: 0.75rem;"><i class="fa-regular fa-clock me-1"></i>2
                      hours ago by Sarah L.</small>
                  </div>
                </div>
                <!-- Ticket 2 -->
                <div class="d-flex align-items-start gap-3 p-3 border rounded-3 bg-white"
                  style="border-color: #f1f5f9 !important;">
                  <img src="https://i.pravatar.cc/150?u=2" class="rounded-circle"
                    style="width: 40px; height: 40px; object-fit: cover;" alt="User">
                  <div class="w-100">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <h6 class="fw-bold m-0" style="color: #2b3674;">Payment Gateway Issue</h6>
                      <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.7rem;">Open</span>
                    </div>
                    <p class="text-muted small mb-1">Stripe integration is failing on checkout.</p>
                    <small class="text-muted" style="font-size: 0.75rem;"><i class="fa-regular fa-clock me-1"></i>5
                      hours ago by Mark D.</small>
                  </div>
                </div>
                <!-- Ticket 3 -->
                <div class="d-flex align-items-start gap-3 p-3 border rounded-3 bg-white"
                  style="border-color: #f1f5f9 !important;">
                  <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                    style="width: 40px; height: 40px; background-color: #10b981;">JD</div>
                  <div class="w-100">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <h6 class="fw-bold m-0" style="color: #2b3674;">Account Deletion</h6>
                      <span class="badge bg-success rounded-pill px-2 py-1" style="font-size: 0.7rem;">Resolved</span>
                    </div>
                    <p class="text-muted small mb-1">Please delete my account and data.</p>
                    <small class="text-muted" style="font-size: 0.75rem;"><i class="fa-regular fa-clock me-1"></i>1 day
                      ago by John Doe</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- System Health -->
          <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0" style="color: #2b3674;">System Health</h5>
                <i class="fa-solid fa-server text-muted"></i>
              </div>

              <!-- CPU Usage -->
              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="d-flex align-items-center gap-2">
                    <div class="rounded d-flex align-items-center justify-content-center"
                      style="width: 32px; height: 32px; background-color: rgba(99, 102, 241, 0.1); color: #6366f1;">
                      <i class="fa-solid fa-microchip"></i>
                    </div>
                    <span class="fw-bold text-dark">CPU Usage</span>
                  </div>
                  <span class="fw-bold" style="color: #6366f1;">65%</span>
                </div>
                <div class="progress"
                  style="height: 8px; border-radius: 4px; background-color: rgba(99, 102, 241, 0.1);">
                  <div class="progress-bar" role="progressbar"
                    style="width: 65%; background-color: #6366f1; border-radius: 4px;"></div>
                </div>
              </div>

              <!-- Memory Usage -->
              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="d-flex align-items-center gap-2">
                    <div class="rounded d-flex align-items-center justify-content-center"
                      style="width: 32px; height: 32px; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
                      <i class="fa-solid fa-memory"></i>
                    </div>
                    <span class="fw-bold text-dark">Memory (RAM)</span>
                  </div>
                  <span class="fw-bold" style="color: #10b981;">42%</span>
                </div>
                <div class="progress"
                  style="height: 8px; border-radius: 4px; background-color: rgba(16, 185, 129, 0.1);">
                  <div class="progress-bar bg-success" role="progressbar" style="width: 42%; border-radius: 4px;"></div>
                </div>
                <small class="text-muted mt-1 d-block text-end">13.4 GB / 32 GB Free</small>
              </div>

              <!-- Disk Space -->
              <div class="mb-0">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="d-flex align-items-center gap-2">
                    <div class="rounded d-flex align-items-center justify-content-center"
                      style="width: 32px; height: 32px; background-color: rgba(239, 68, 68, 0.1); color: #ef4444;">
                      <i class="fa-solid fa-hard-drive"></i>
                    </div>
                    <span class="fw-bold text-dark">SSD Storage</span>
                  </div>
                  <span class="fw-bold" style="color: #ef4444;">88%</span>
                </div>
                <div class="progress"
                  style="height: 8px; border-radius: 4px; background-color: rgba(239, 68, 68, 0.1);">
                  <div class="progress-bar bg-danger" role="progressbar" style="width: 88%; border-radius: 4px;"></div>
                </div>
                <small class="text-danger mt-1 d-block text-end fw-bold">Warning: Storage getting full</small>
              </div>

            </div>
          </div>
        </div>
      </div>


@endsection
<?php

declare(strict_types=1);

require_once __DIR__ . '/../auth_helpers.php';

start_auth_session();

$page = 'applications';
$title = 'Applications'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title><?= $title ?> | Paw Net</title>
     <link rel="stylesheet" href="style.css">
</head>

<body>
     <?php include 'sidebar.php'; ?><div class="main"><?php include 'header.php'; ?><main class="content">
               <div class="welcome-row">
                    <div>
                         <div class="eyebrow">ADOPTION MANAGEMENT</div>
                         <h1>Applications 📝</h1>
                         <p>Review and update adopter requests.</p>
                    </div>
               </div>
               <section class="panel">
                    <div class="panel-heading">
                         <div>
                              <h2>Adoption Requests</h2>
                              <p>Use the status menu to update a sample request.</p>
                         </div><input class="table-search" id="applicationSearch" placeholder="Search applications...">
                    </div>
                    <div class="table-wrap">
                         <table id="applicationsTable">
                              <thead>
                                   <tr>
                                        <th>Pet</th>
                                        <th>Adopter</th>
                                        <th>Applied on</th>
                                        <th>Status</th>
                                        <th>Update status</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   <tr>
                                        <td><span class="pet-name"><img src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=500" alt="Bruno"
                                                       class="pet-image">
                                                  Bruno</span><small>Labrador</small></td>
                                        <td>Aarav Sharma</td>
                                        <td>Oct 8, 2026</td>
                                        <td><span class="status review">Under Review</span></td>
                                        <td><select class="status-select">
                                                  <option>Under Review</option>
                                                  <option>Pending</option>
                                                  <option>Approved</option>
                                                  <option>Rejected</option>
                                             </select></td>
                                   </tr>
                                   <tr>
                                        <td><span class="pet-name"><img src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=500" alt="Bruno"
                                                       class="pet-image">
                                                  Bruno</span><small>Persian Cat</small></td>
                                        <td>Diya Patel</td>
                                        <td>Oct 7, 2026</td>
                                        <td><span class="status approved">Approved</span></td>
                                        <td><select class="status-select">
                                                  <option>Approved</option>
                                                  <option>Under Review</option>
                                                  <option>Pending</option>
                                                  <option>Rejected</option>
                                             </select></td>
                                   </tr>
                                   <tr>
                                        <td><span class="pet-name"><img src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=500" alt="Bruno"
                                                       class="pet-image">
                                                  Bruno</span><small>Indie</small></td>
                                        <td>Rohan Das</td>
                                        <td>Oct 6, 2026</td>
                                        <td><span class="status pending">Pending</span></td>
                                        <td><select class="status-select">
                                                  <option>Pending</option>
                                                  <option>Under Review</option>
                                                  <option>Approved</option>
                                                  <option>Rejected</option>
                                             </select></td>
                                   </tr>
                                   <tr>
                                        <td><span class="pet-name"><img src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=500" alt="Bruno"
                                                       class="pet-image">
                                                  Bruno</span><small>Hamster</small></td>
                                        <td>Meera Rao</td>
                                        <td>Oct 5, 2026</td>
                                        <td><span class="status rejected">Rejected</span></td>
                                        <td><select class="status-select">
                                                  <option>Rejected</option>
                                                  <option>Pending</option>
                                                  <option>Under Review</option>
                                                  <option>Approved</option>
                                             </select></td>
                                   </tr>
                              </tbody>
                         </table>
                    </div>
                    <p class="hint">Demo only: status changes are not saved to a database.</p>
               </section>
          </main>
     </div>
     <script src="script.js"></script>
</body>

</html>
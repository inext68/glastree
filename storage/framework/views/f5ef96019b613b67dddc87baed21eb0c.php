<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Glastree'); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap">
    <?php echo $__env->yieldContent('styles'); ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        <?php $unread = Auth::user()->unreadNotificheCount() ?? 0 ?>
                        <?php if($unread > 0): ?>
                        <span class="badge badge-warning navbar-badge"><?php echo e($unread); ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <span class="dropdown-header">Notifiche (<?php echo e($unread); ?>)</span>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">Visualizza tutte</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fas fa-user"></i> <?php echo e(Auth::user()->name); ?>

                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user-cog mr-2"></i> Profilo
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-2"></i> Esci
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="<?php echo e(route('dashboard')); ?>" class="brand-link">
                <i class="fas fa-tree brand-image ml-3 mr-2"></i>
                <span class="brand-text font-weight-light">Glastree</span>
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="<?php echo e(route('dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('individui.index')); ?>" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Individui</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('gruppi.index')); ?>" class="nav-link">
                                <i class="nav-icon fas fa-folder"></i>
                                <p>Gruppi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('documenti.index')); ?>" class="nav-link">
                                <i class="nav-icon fas fa-file"></i>
                                <p>Documenti</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('eventi.index')); ?>" class="nav-link">
                                <i class="nav-icon fas fa-calendar"></i>
                                <p>Eventi</p>
                            </a>
                        </li>
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-envelope"></i>
                                <p>Mailing <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo e(route('mailing-liste.index')); ?>" class="nav-link">
                                        <i class="nav-icon fas fa-list"></i>
                                        <p>Mailing Lists</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('mailing.invio')); ?>" class="nav-link">
                                        <i class="nav-icon fas fa-paper-plane"></i>
                                        <p>Invio Mailing</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('viste.index')); ?>" class="nav-link">
                                <i class="nav-icon fas fa-bookmark"></i>
                                <p>Utilità Viste</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Report</p>
                            </a>
                        </li>
                        <?php if(Auth::user()->is_admin): ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-cog"></i>
                                <p>Impostazioni</p>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?php echo $__env->yieldContent('page_title', 'Dashboard'); ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <?php echo $__env->yieldContent('breadcrumbs'); ?>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    <?php echo $slot ?? ''; ?>

                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </div>
        </div>

        <footer class="main-footer">
            <strong>&copy; <?php echo e(date('Y')); ?> Glastree</strong>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html><?php /**PATH /var/www/html/glastree/resources/views/layouts/adminlte.blade.php ENDPATH**/ ?>
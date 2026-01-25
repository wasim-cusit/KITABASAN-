<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Teacher - KITAB ASAN')</title>

    <!-- Vite Assets (Tailwind CSS, Alpine.js, AOS, etc.) - Load first -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Bootstrap CSS - Load after Tailwind to allow overrides -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Custom Styles -->
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        @media (max-width: 768px) {
            .sidebar-overlay {
                display: none;
            }
            .sidebar-overlay.active {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 40;
            }
        }

        /* Bootstrap Modal Backdrop Enhancement */
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.6) !important;
            z-index: 1040;
        }

        .modal-backdrop.show {
            opacity: 1;
        }

        /* Remove rounded borders from all form inputs - Force override */
        .modal .form-control,
        .modal .form-select,
        .modal input[type="text"],
        .modal input[type="number"],
        .modal input[type="email"],
        .modal input[type="password"],
        .modal textarea,
        .modal select {
            border-radius: 0 !important;
            -webkit-border-radius: 0 !important;
            -moz-border-radius: 0 !important;
        }

        /* Remove any rounded classes that might be applied */
        .modal .rounded-lg,
        .modal .rounded-xl,
        .modal .rounded-md,
        .modal .rounded {
            border-radius: 0 !important;
        }

        /* Remove rounded borders from buttons in modals */
        .modal .btn,
        .modal button {
            border-radius: 0 !important;
        }

        /* Ensure modal content has no rounded corners */
        .modal-content,
        .modal-header,
        .modal-footer {
            border-radius: 0 !important;
        }

        /* Modal footer button alignment - Proper spacing for laptop screens */
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            padding: 1rem;
            border-top: 1px solid #dee2e6;
        }

        .modal-footer .btn {
            min-width: 100px;
            padding: 0.5rem 1.25rem;
            border-radius: 0 !important;
            font-weight: 500;
        }

        /* Ensure proper button order: Cancel on left, Submit on right */
        .modal-footer .btn-secondary {
            margin-right: auto;
        }

        @media (min-width: 576px) {
            .modal-footer .btn-secondary {
                margin-right: 0;
            }
        }

        /* Ensure buttons are side by side on desktop */
        @media (min-width: 576px) {
            .modal-footer {
                flex-direction: row;
                justify-content: flex-end;
            }

            .modal-footer .btn-secondary {
                order: 1;
            }

            .modal-footer .btn-primary {
                order: 2;
            }
        }

        /* Ensure modal is properly centered and sized */
        .modal-dialog {
            max-width: 800px;
        }

        @media (max-width: 768px) {
            .modal-dialog {
                max-width: 95%;
                margin: 1rem auto;
            }

            .modal-footer {
                flex-direction: column;
            }

            .modal-footer .btn {
                width: 100%;
            }
        }

        /* Teacher sidebar nav - match admin/super admin, ensure Bootstrap doesn't override */
        .teacher-layout-sidebar {
            background-color: #1f2937 !important;
            color: #ffffff !important;
            flex-shrink: 0;
            min-height: 100vh;
        }
        .teacher-layout-sidebar nav.space-y-2 > * + * {
            margin-top: 0.5rem;
        }
        .teacher-layout-sidebar nav a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            color: #e5e7eb;
            text-decoration: none;
            transition: background-color 0.15s, color 0.15s;
        }
        .teacher-layout-sidebar nav a:hover {
            background-color: #374151 !important;
            color: #ffffff !important;
        }
        .teacher-layout-sidebar nav a.bg-gray-700 {
            background-color: #374151 !important;
            color: #ffffff !important;
        }
        .teacher-layout-sidebar nav a svg {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
        }
        .teacher-layout-sidebar .border-gray-700 {
            border-color: #374151 !important;
        }
        .teacher-layout-sidebar .text-gray-400 {
            color: #9ca3af !important;
        }

        /* Teacher layout root - min-height and flex (fallback if Tailwind min-h-screen/grid is lost) */
        .teacher-layout-root {
            min-height: 100vh;
            display: flex;
            --teacher-header-height: 6rem;
        }
        /* Main content stays to the right of the fixed sidebar on desktop (never underneath) */
        @media (min-width: 1024px) {
            .teacher-layout-main {
                margin-left: 16rem;
                max-width: calc(100% - 16rem);
            }
        }
        .teacher-layout-content {
            padding-top: var(--teacher-header-height);
        }
        /* Teacher layout: top bar / header - fixed so it does NOT scroll with the page */
        .teacher-layout-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 30;
            background-color: #ffffff;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }
        @media (min-width: 1024px) {
            .teacher-layout-header {
                left: 16rem; /* align with main content, to the right of the sidebar */
                right: 0;
            }
        }
        .teacher-layout-header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 1rem;
        }
        @media (min-width: 1024px) {
            .teacher-layout-header-inner {
                padding: 0.5rem 1.5rem;
            }
        }
        .teacher-layout-header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .teacher-layout-header-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1f2937;
        }
        @media (min-width: 1024px) {
            .teacher-layout-header-title {
                font-size: 1.25rem;
            }
        }
        /* Teacher dashboard: stats grid and cards - explicit grid/flex so layout is correct */
        .teacher-dashboard-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        @media (min-width: 1024px) {
            .teacher-dashboard-stats {
                grid-template-columns: repeat(4, 1fr);
                gap: 1.5rem;
                margin-bottom: 2rem;
            }
        }
        .teacher-dashboard-stat-card {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            border: 1px solid #f3f4f6;
            padding: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        .teacher-dashboard-stat-label {
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .teacher-dashboard-stat-value {
            color: #0f172a;
            font-weight: 700;
        }
        .teacher-dashboard-stat-value--amber {
            color: #b45309;
        }
        .teacher-dashboard-welcome-title {
            color: #0f172a;
            font-weight: 700;
        }
        .teacher-dashboard-welcome-subtitle {
            color: #64748b;
            font-size: 0.875rem;
        }
        @media (min-width: 1024px) {
            .teacher-dashboard-stat-card {
                padding: 1.5rem;
            }
        }
        .teacher-dashboard-container {
            max-width: 80rem;
            margin-left: auto;
            margin-right: auto;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        @media (min-width: 640px) {
            .teacher-dashboard-container {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
        }
        @media (min-width: 1024px) {
            .teacher-dashboard-container {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }

        /* Teacher dashboard: Quick action buttons - proper shape, no rounded border */
        .teacher-dashboard-quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        @media (min-width: 1024px) {
            .teacher-dashboard-quick-actions {
                margin-bottom: 2rem;
            }
        }
        .teacher-dashboard-quick-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0;
            text-decoration: none;
            transition: background-color 0.15s, border-color 0.15s, color 0.15s;
            cursor: pointer;
            border: 1px solid transparent;
        }
        .teacher-dashboard-quick-action:focus {
            outline: none;
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px #3b82f6;
        }
        .teacher-dashboard-quick-action--primary {
            background-color: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }
        .teacher-dashboard-quick-action--primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
        }
        .teacher-dashboard-quick-action--secondary {
            background-color: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        .teacher-dashboard-quick-action--secondary:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
            color: #374151;
        }
        .teacher-dashboard-quick-action svg {
            width: 1rem;
            height: 1rem;
            flex-shrink: 0;
        }

        /* Teacher courses index: Create New Course button - explicit styles, no rounded border */
        .teacher-courses-create-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            background-color: #2563eb;
            color: #ffffff;
            border: 1px solid #2563eb;
            border-radius: 0;
            text-decoration: none;
            transition: background-color 0.15s, border-color 0.15s, color 0.15s;
        }
        .teacher-courses-create-btn:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
        }
        .teacher-courses-create-btn:focus {
            outline: none;
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px #3b82f6;
        }
        @media (min-width: 1024px) {
            .teacher-courses-create-btn {
                padding: 0.5rem 1.5rem;
                font-size: 1rem;
            }
        }

        /* Teacher courses index: page container, header, grid, cards */
        .teacher-courses-container {
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 0;
            padding-right: 0;
        }
        @media (min-width: 1024px) {
            .teacher-courses-container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
        .teacher-courses-header {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        @media (min-width: 640px) {
            .teacher-courses-header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .teacher-courses-header-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e3a5f;
        }
        @media (min-width: 1024px) {
            .teacher-courses-header-title {
                font-size: 1.875rem;
            }
        }
        .teacher-courses-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        @media (min-width: 768px) {
            .teacher-courses-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (min-width: 1024px) {
            .teacher-courses-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 1.5rem;
            }
        }
        .teacher-courses-card {
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            overflow: hidden;
            transition: box-shadow 0.2s;
        }
        .teacher-courses-card:hover {
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }
        .teacher-courses-card-cover {
            flex-shrink: 0;
            height: 10rem;
            background: linear-gradient(to bottom right, #60a5fa, #4f46e5);
            position: relative;
        }
        @media (min-width: 1024px) {
            .teacher-courses-card-cover {
                height: 12rem;
            }
        }
        .teacher-courses-card-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .teacher-courses-card-status {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.25rem;
        }
        @media (min-width: 1024px) {
            .teacher-courses-card-status {
                top: 1rem;
                right: 1rem;
            }
        }
        .teacher-courses-card-status--published {
            background-color: #22c55e;
            color: #ffffff;
        }
        .teacher-courses-card-status--draft {
            background-color: #eab308;
            color: #ffffff;
        }
        .teacher-courses-card-body {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            padding: 1rem;
        }
        @media (min-width: 1024px) {
            .teacher-courses-card-body {
                padding: 1.5rem;
            }
        }
        .teacher-courses-card-content {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .teacher-courses-card-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }
        @media (min-width: 1024px) {
            .teacher-courses-card-title {
                font-size: 1.25rem;
            }
        }
        .teacher-courses-card-desc {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 0.75rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        @media (min-width: 1024px) {
            .teacher-courses-card-desc {
                font-size: 0.875rem;
                margin-bottom: 1rem;
            }
        }
        .teacher-courses-card-meta {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }
        @media (min-width: 640px) {
            .teacher-courses-card-meta {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                gap: 0.75rem;
            }
        }
        @media (min-width: 1024px) {
            .teacher-courses-card-meta {
                margin-bottom: 1rem;
                gap: 1rem;
            }
        }
        .teacher-courses-card-grade {
            font-size: 0.75rem;
            color: #64748b;
        }
        @media (min-width: 640px) {
            .teacher-courses-card-grade {
                flex: 1;
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        }
        @media (min-width: 1024px) {
            .teacher-courses-card-grade {
                font-size: 0.875rem;
            }
        }
        .teacher-courses-card-price {
            flex-shrink: 0;
            align-self: flex-end;
            font-size: 0.75rem;
            font-weight: 600;
        }
        @media (min-width: 640px) {
            .teacher-courses-card-price {
                align-self: center;
            }
        }
        @media (min-width: 1024px) {
            .teacher-courses-card-price {
                font-size: 0.875rem;
            }
        }
        .teacher-courses-card-price--free {
            color: #16a34a;
        }
        .teacher-courses-card-price--paid {
            color: #2563eb;
        }
        .teacher-courses-card-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }
        @media (min-width: 640px) {
            .teacher-courses-card-actions {
                flex-direction: row;
            }
        }
        .teacher-courses-card-btn {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.5rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0;
            border: 1px solid transparent;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.15s, border-color 0.15s, color 0.15s;
        }
        @media (min-width: 1024px) {
            .teacher-courses-card-btn {
                padding: 0.5rem 1rem;
                font-size: 1rem;
            }
        }
        .teacher-courses-card-btn--primary {
            background-color: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }
        .teacher-courses-card-btn--primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
        }
        .teacher-courses-card-btn--primary:focus {
            outline: none;
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px #3b82f6;
        }
        .teacher-courses-card-btn--secondary {
            background-color: #f9fafb;
            color: #374151;
            border-color: #d1d5db;
        }
        .teacher-courses-card-btn--secondary:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
            color: #374151;
        }
        .teacher-courses-card-btn--secondary:focus {
            outline: none;
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px #9ca3af;
        }
        .teacher-courses-pagination {
            margin-top: 1.5rem;
        }
        .teacher-courses-empty {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            padding: 1.5rem;
            text-align: center;
        }
        @media (min-width: 1024px) {
            .teacher-courses-empty {
                padding: 3rem;
            }
        }
        .teacher-courses-empty-icon {
            margin-left: auto;
            margin-right: auto;
            width: 4rem;
            height: 4rem;
            color: #9ca3af;
        }
        @media (min-width: 1024px) {
            .teacher-courses-empty-icon {
                width: 6rem;
                height: 6rem;
            }
        }
        .teacher-courses-empty-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }
        @media (min-width: 1024px) {
            .teacher-courses-empty-title {
                font-size: 1.5rem;
            }
        }
        .teacher-courses-empty-desc {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 1rem;
        }
        @media (min-width: 1024px) {
            .teacher-courses-empty-desc {
                font-size: 1rem;
                margin-bottom: 1.5rem;
            }
        }

        /* Teacher students index: container, card, header, search, table, View Details link */
        .teacher-students-container {
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 0;
            padding-right: 0;
        }
        @media (min-width: 1024px) {
            .teacher-students-container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
        .teacher-students-card {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            padding: 1rem;
        }
        @media (min-width: 1024px) {
            .teacher-students-card {
                padding: 1.5rem;
            }
        }
        .teacher-students-header {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        @media (min-width: 640px) {
            .teacher-students-header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .teacher-students-header-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e3a5f;
        }
        @media (min-width: 1024px) {
            .teacher-students-header-title {
                font-size: 1.5rem;
            }
        }
        .teacher-students-search {
            margin-bottom: 1.5rem;
        }
        .teacher-students-search-inner {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        @media (min-width: 640px) {
            .teacher-students-search-inner {
                flex-direction: row;
            }
        }
        .teacher-students-search-input {
            flex: 1;
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0;
            font-size: 0.875rem;
        }
        @media (min-width: 1024px) {
            .teacher-students-search-input {
                font-size: 1rem;
            }
        }
        .teacher-students-search-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            background-color: #2563eb;
            color: #ffffff;
            border: 1px solid #2563eb;
            border-radius: 0;
            cursor: pointer;
            transition: background-color 0.15s, border-color 0.15s;
        }
        .teacher-students-search-btn:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }
        @media (min-width: 1024px) {
            .teacher-students-search-btn {
                font-size: 1rem;
            }
        }
        .teacher-students-clear-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            background-color: #4b5563;
            color: #ffffff;
            border: 1px solid #4b5563;
            border-radius: 0;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.15s, border-color 0.15s;
        }
        .teacher-students-clear-btn:hover {
            background-color: #374151;
            border-color: #374151;
            color: #ffffff;
        }
        @media (min-width: 1024px) {
            .teacher-students-clear-btn {
                font-size: 1rem;
            }
        }
        .teacher-students-table-wrap {
            overflow-x: auto;
            margin-left: -1rem;
            margin-right: -1rem;
        }
        @media (min-width: 1024px) {
            .teacher-students-table-wrap {
                margin-left: 0;
                margin-right: 0;
            }
        }
        .teacher-students-table-inner {
            display: inline-block;
            min-width: 100%;
            vertical-align: middle;
        }
        .teacher-students-table-outer {
            overflow: hidden;
            box-shadow: 0 0 0 1px rgb(0 0 0 / 0.05);
        }
        @media (min-width: 768px) {
            .teacher-students-table-outer {
                border-radius: 0.5rem;
            }
        }
        .teacher-students-table {
            width: 100%;
            min-width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }
        .teacher-students-table thead {
            background-color: #f9fafb;
        }
        .teacher-students-table th {
            padding: 0.75rem 0.75rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        @media (min-width: 1024px) {
            .teacher-students-table th {
                padding: 0.75rem 1.5rem;
            }
        }
        .teacher-students-table th.hide-sm { display: none; }
        @media (min-width: 640px) {
            .teacher-students-table th.hide-sm { display: table-cell; }
        }
        .teacher-students-table th.hide-md { display: none; }
        @media (min-width: 768px) {
            .teacher-students-table th.hide-md { display: table-cell; }
        }
        .teacher-students-table tbody tr {
            border-top: 1px solid #e5e7eb;
        }
        .teacher-students-table td {
            padding: 1rem 0.75rem;
            font-size: 0.75rem;
            vertical-align: middle;
        }
        @media (min-width: 1024px) {
            .teacher-students-table td {
                padding: 1rem 1.5rem;
                font-size: 0.875rem;
            }
        }
        .teacher-students-table td.hide-sm { display: none; }
        @media (min-width: 640px) {
            .teacher-students-table td.hide-sm { display: table-cell; }
        }
        .teacher-students-table td.hide-md { display: none; }
        @media (min-width: 768px) {
            .teacher-students-table td.hide-md { display: table-cell; }
        }
        .teacher-students-table td.whitespace-nowrap {
            white-space: nowrap;
        }
        .teacher-students-table td.teacher-students-cell-muted {
            color: #64748b;
        }
        .teacher-students-cell-student {
            display: flex;
            align-items: center;
        }
        .teacher-students-avatar {
            flex-shrink: 0;
            width: 2rem;
            height: 2rem;
        }
        @media (min-width: 1024px) {
            .teacher-students-avatar {
                width: 2.5rem;
                height: 2.5rem;
            }
        }
        .teacher-students-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .teacher-students-name-wrap {
            margin-left: 0.5rem;
        }
        @media (min-width: 1024px) {
            .teacher-students-name-wrap {
                margin-left: 1rem;
            }
        }
        .teacher-students-name {
            font-size: 0.75rem;
            font-weight: 600;
            color: #0f172a;
        }
        @media (min-width: 1024px) {
            .teacher-students-name {
                font-size: 0.875rem;
            }
        }
        .teacher-students-email-mobile {
            font-size: 0.75rem;
            color: #64748b;
        }
        @media (min-width: 640px) {
            .teacher-students-email-mobile {
                display: none;
            }
        }
        .teacher-students-badge {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.25;
            white-space: nowrap;
            border-radius: 0;
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }
        @media (min-width: 1024px) {
            .teacher-students-badge {
                font-size: 0.8125rem;
            }
        }
        .teacher-students-view-link {
            font-size: 0.75rem;
            font-weight: 500;
            color: #2563eb;
            text-decoration: none;
            transition: color 0.15s;
        }
        .teacher-students-view-link:hover {
            color: #1e3a8a;
        }
        .teacher-students-view-link:focus {
            outline: none;
            text-decoration: underline;
        }
        @media (min-width: 1024px) {
            .teacher-students-view-link {
                font-size: 0.875rem;
            }
        }
        .teacher-students-pagination {
            margin-top: 1rem;
        }
        .teacher-students-empty {
            text-align: center;
            font-size: 0.875rem;
            color: #64748b;
        }
        .teacher-students-table td.teacher-students-empty {
            padding: 1.5rem 1rem;
        }
        @media (min-width: 1024px) {
            .teacher-students-empty {
                font-size: 1rem;
            }
        }

        /* Teacher devices index: container, card, header, sections, table */
        .teacher-devices-container {
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 0;
            padding-right: 0;
        }
        @media (min-width: 1024px) {
            .teacher-devices-container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
        .teacher-devices-card {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            padding: 1rem;
        }
        @media (min-width: 1024px) {
            .teacher-devices-card {
                padding: 1.5rem;
            }
        }
        .teacher-devices-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding: 0.75rem 1rem;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 0;
        }
        @media (min-width: 1024px) {
            .teacher-devices-header {
                padding: 0.75rem 1.25rem;
            }
        }
        .teacher-devices-header-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            line-height: 1.3;
            color: #1e3a5f;
            letter-spacing: -0.02em;
        }
        @media (min-width: 1024px) {
            .teacher-devices-header-title {
                font-size: 1.5rem;
            }
        }
        .teacher-devices-active {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        @media (min-width: 1024px) {
            .teacher-devices-active {
                padding: 1.5rem;
            }
        }
        .teacher-devices-active-inner {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        @media (min-width: 640px) {
            .teacher-devices-active-inner {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .teacher-devices-active-title {
            font-size: 1rem;
            font-weight: 600;
            color: #166534;
            margin-bottom: 0.5rem;
        }
        @media (min-width: 1024px) {
            .teacher-devices-active-title {
                font-size: 1.125rem;
            }
        }
        .teacher-devices-active-detail {
            font-size: 0.75rem;
            color: #475569;
        }
        @media (min-width: 1024px) {
            .teacher-devices-active-detail {
                font-size: 0.875rem;
            }
        }
        .teacher-devices-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0;
            white-space: nowrap;
        }
        @media (min-width: 1024px) {
            .teacher-devices-badge {
                font-size: 0.875rem;
            }
        }
        .teacher-devices-badge--active {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .teacher-devices-badge--pending {
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fde047;
        }
        .teacher-devices-badge--blocked {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        .teacher-devices-badge--default {
            background-color: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        .teacher-devices-reset-block {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #bbf7d0;
        }
        .teacher-devices-reset-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e3a5f;
            margin-bottom: 0.5rem;
        }
        @media (min-width: 1024px) {
            .teacher-devices-reset-title {
                font-size: 1rem;
            }
        }
        .teacher-devices-reset-desc {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 1rem;
        }
        @media (min-width: 1024px) {
            .teacher-devices-reset-desc {
                font-size: 0.875rem;
            }
        }
        .teacher-devices-reset-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            background-color: #ca8a04;
            color: #ffffff;
            border: 1px solid #ca8a04;
            border-radius: 0;
            cursor: pointer;
            transition: background-color 0.15s, border-color 0.15s;
        }
        .teacher-devices-reset-btn:hover {
            background-color: #a16207;
            border-color: #a16207;
        }
        @media (min-width: 1024px) {
            .teacher-devices-reset-btn {
                font-size: 1rem;
            }
        }
        .teacher-devices-pending {
            background-color: #fefce8;
            border: 1px solid #fde047;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        @media (min-width: 1024px) {
            .teacher-devices-pending {
                padding: 1.5rem;
            }
        }
        .teacher-devices-pending-title {
            font-size: 1rem;
            font-weight: 600;
            color: #854d0e;
            margin-bottom: 0.5rem;
        }
        @media (min-width: 1024px) {
            .teacher-devices-pending-title {
                font-size: 1.125rem;
            }
        }
        .teacher-devices-pending-detail {
            font-size: 0.75rem;
            color: #475569;
            margin-bottom: 0.5rem;
        }
        @media (min-width: 1024px) {
            .teacher-devices-pending-detail {
                font-size: 0.875rem;
            }
        }
        .teacher-devices-pending-note {
            font-size: 0.75rem;
            color: #854d0e;
        }
        @media (min-width: 1024px) {
            .teacher-devices-pending-note {
                font-size: 0.875rem;
            }
        }
        .teacher-devices-history {
            margin-top: 1.5rem;
        }
        .teacher-devices-history-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1rem;
        }
        @media (min-width: 1024px) {
            .teacher-devices-history-title {
                font-size: 1.25rem;
            }
        }
        .teacher-devices-table-wrap {
            overflow-x: auto;
            margin-left: -1rem;
            margin-right: -1rem;
        }
        @media (min-width: 1024px) {
            .teacher-devices-table-wrap {
                margin-left: 0;
                margin-right: 0;
            }
        }
        .teacher-devices-table-inner {
            display: inline-block;
            min-width: 100%;
            vertical-align: middle;
        }
        .teacher-devices-table-outer {
            overflow: hidden;
            box-shadow: 0 0 0 1px rgb(0 0 0 / 0.05);
        }
        @media (min-width: 768px) {
            .teacher-devices-table-outer {
                border-radius: 0.5rem;
            }
        }
        .teacher-devices-table {
            width: 100%;
            min-width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }
        .teacher-devices-table thead {
            background-color: #f9fafb;
        }
        .teacher-devices-table th {
            padding: 0.75rem 0.75rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        @media (min-width: 1024px) {
            .teacher-devices-table th {
                padding: 0.75rem 1.5rem;
            }
        }
        .teacher-devices-table th.hide-md { display: none; }
        @media (min-width: 768px) {
            .teacher-devices-table th.hide-md { display: table-cell; }
        }
        .teacher-devices-table th.hide-lg { display: none; }
        @media (min-width: 1024px) {
            .teacher-devices-table th.hide-lg { display: table-cell; }
        }
        .teacher-devices-table tbody tr {
            border-top: 1px solid #e5e7eb;
        }
        .teacher-devices-table td {
            padding: 1rem 0.75rem;
            font-size: 0.75rem;
            vertical-align: middle;
        }
        @media (min-width: 1024px) {
            .teacher-devices-table td {
                padding: 1rem 1.5rem;
                font-size: 0.875rem;
            }
        }
        .teacher-devices-table td.hide-md { display: none; }
        @media (min-width: 768px) {
            .teacher-devices-table td.hide-md { display: table-cell; }
        }
        .teacher-devices-table td.hide-lg { display: none; }
        @media (min-width: 1024px) {
            .teacher-devices-table td.hide-lg { display: table-cell; }
        }
        .teacher-devices-table td.whitespace-nowrap {
            white-space: nowrap;
        }
        .teacher-devices-table td.teacher-devices-cell-muted {
            color: #6b7280;
        }
        .teacher-devices-table .teacher-devices-ip-mobile {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
        }
        @media (min-width: 768px) {
            .teacher-devices-table .teacher-devices-ip-mobile {
                display: none;
            }
        }
        .teacher-devices-empty {
            text-align: center;
            color: #64748b;
            font-size: 0.875rem;
        }
        .teacher-devices-table td.teacher-devices-empty {
            padding: 1.5rem 1rem;
        }
        @media (min-width: 1024px) {
            .teacher-devices-empty {
                font-size: 1rem;
            }
        }

        /* Teacher profile show: card, header, sections, grid, form */
        .teacher-profile-container {
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 0;
            padding-right: 0;
        }
        @media (min-width: 1024px) {
            .teacher-profile-container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
        .teacher-profile-card {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            padding: 1rem;
            overflow: visible;
        }
        @media (min-width: 1024px) {
            .teacher-profile-card {
                padding: 1.5rem;
            }
        }
        .teacher-profile-header {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        @media (min-width: 640px) {
            .teacher-profile-header {
                flex-direction: row;
                justify-content: space-between;
                align-items: flex-start;
            }
        }
        .teacher-profile-hero {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .teacher-profile-avatar {
            width: 6rem;
            height: 6rem;
            border-radius: 50%;
            border: 4px solid #dbeafe;
            flex-shrink: 0;
            object-fit: cover;
        }
        .teacher-profile-avatar-placeholder {
            width: 6rem;
            height: 6rem;
            border-radius: 50%;
            border: 4px solid #dbeafe;
            background: linear-gradient(to bottom right, #3b82f6, #9333ea);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .teacher-profile-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
        }
        @media (min-width: 1024px) {
            .teacher-profile-name {
                font-size: 1.5rem;
            }
        }
        .teacher-profile-email {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.25rem;
        }
        .teacher-profile-edit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            background-color: #2563eb;
            color: #ffffff;
            border: 1px solid #2563eb;
            border-radius: 0;
            text-decoration: none;
            transition: background-color 0.15s, border-color 0.15s;
        }
        .teacher-profile-edit-btn:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
        }
        @media (min-width: 1024px) {
            .teacher-profile-edit-btn {
                font-size: 1rem;
            }
        }
        .teacher-profile-section {
            border-top: 1px solid #e5e7eb;
            padding-top: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .teacher-profile-section:last-of-type {
            margin-bottom: 0;
        }
        .teacher-profile-section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e3a5f;
            margin: 0 0 1rem 0;
            border-left: 4px solid #2563eb;
            padding-left: 0.75rem;
        }
        .teacher-profile-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        @media (min-width: 768px) {
            .teacher-profile-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .teacher-profile-field {
            min-width: 0;
        }
        .teacher-profile-field--full {
            grid-column: 1 / -1;
        }
        @media (min-width: 768px) {
            .teacher-profile-field--full {
                grid-column: 1 / -1;
            }
        }
        .teacher-profile-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.25rem;
        }
        .teacher-profile-value {
            font-size: 1rem;
            color: #0f172a;
            font-weight: 400;
        }
        .teacher-profile-value--pre {
            white-space: pre-line;
        }
        .teacher-profile-password-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        @media (min-width: 768px) {
            .teacher-profile-password-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        .teacher-profile-input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0;
            font-size: 0.875rem;
        }
        .teacher-profile-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }
        .teacher-profile-input.border-red-500 {
            border-color: #ef4444;
        }
        .teacher-profile-error {
            font-size: 0.875rem;
            color: #ef4444;
            margin-top: 0.25rem;
        }
        .teacher-profile-submit-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            background-color: #2563eb;
            color: #ffffff;
            border: 1px solid #2563eb;
            border-radius: 0;
            cursor: pointer;
            margin-top: 1rem;
            transition: background-color 0.15s, border-color 0.15s;
        }
        .teacher-profile-submit-btn:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        /* Teacher settings index: container, card, sections, checkboxes, device block, submit */
        .teacher-settings-container {
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 0;
            padding-right: 0;
        }
        @media (min-width: 1024px) {
            .teacher-settings-container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
        .teacher-settings-card {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            padding: 1rem;
        }
        @media (min-width: 1024px) {
            .teacher-settings-card {
                padding: 1.5rem 2rem;
            }
        }
        .teacher-settings-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e3a5f;
            margin: 0 0 1.5rem 0;
        }
        @media (min-width: 1024px) {
            .teacher-settings-title {
                font-size: 1.5rem;
                margin-bottom: 2rem;
            }
        }
        .teacher-settings-sections {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        .teacher-settings-section {
            border-top: 1px solid #e5e7eb;
            padding-top: 1.5rem;
        }
        .teacher-settings-section:first-child {
            border-top: none;
            padding-top: 0;
        }
        .teacher-settings-section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e3a5f;
            margin: 0 0 1rem 0;
        }
        @media (min-width: 1024px) {
            .teacher-settings-section-title {
                font-size: 1.25rem;
                margin-bottom: 1.25rem;
            }
        }
        .teacher-settings-options {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .teacher-settings-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .teacher-settings-option input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
            flex-shrink: 0;
            accent-color: #2563eb;
        }
        .teacher-settings-option span {
            font-size: 0.9375rem;
            color: #374151;
        }
        .teacher-settings-device-block {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 0.5rem;
            padding: 1rem;
        }
        @media (min-width: 1024px) {
            .teacher-settings-device-block {
                padding: 1.25rem;
            }
        }
        .teacher-settings-device-desc {
            font-size: 0.875rem;
            color: #475569;
            margin: 0 0 1rem 0;
        }
        @media (min-width: 1024px) {
            .teacher-settings-device-desc {
                font-size: 1rem;
                margin-bottom: 1.25rem;
            }
        }
        .teacher-settings-device-link {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            background-color: #2563eb;
            color: #ffffff;
            border: 1px solid #2563eb;
            border-radius: 0;
            text-decoration: none;
            transition: background-color 0.15s, border-color 0.15s;
        }
        .teacher-settings-device-link:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
        }
        @media (min-width: 1024px) {
            .teacher-settings-device-link {
                font-size: 1rem;
                padding: 0.5rem 1.25rem;
            }
        }
        .teacher-settings-submit-wrap {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }
        .teacher-settings-submit-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            background-color: #2563eb;
            color: #ffffff;
            border: 1px solid #2563eb;
            border-radius: 0;
            cursor: pointer;
            transition: background-color 0.15s, border-color 0.15s;
        }
        .teacher-settings-submit-btn:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }
        @media (min-width: 1024px) {
            .teacher-settings-submit-btn {
                font-size: 1rem;
                padding: 0.5rem 1.5rem;
            }
        }

        /* Teacher chatbot: root, header, icon, messages, input */
        .teacher-chatbot-root {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            height: calc(100vh - 8rem);
            min-height: 20rem;
            display: flex;
            flex-direction: column;
        }
        .teacher-chatbot-header {
            background: linear-gradient(to right, #2563eb, #4f46e5);
            color: #ffffff;
            padding: 1rem;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }
        .teacher-chatbot-header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .teacher-chatbot-header-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .teacher-chatbot-header-icon {
            width: 2.5rem;
            height: 2.5rem;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .teacher-chatbot-header-icon svg {
            width: 1.5rem;
            height: 1.5rem;
        }
        .teacher-chatbot-header-text h3 {
            font-size: 1.125rem;
            font-weight: 600;
            color: #ffffff;
            margin: 0 0 0.125rem 0;
        }
        .teacher-chatbot-header-text p {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.8);
            margin: 0;
        }
        .teacher-chatbot-messages {
            flex: 1;
            min-height: 18rem;
            overflow-y: auto;
            padding: 1rem;
            background-color: #f9fafb;
        }
        .teacher-chatbot-welcome {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100%;
            text-align: center;
            padding: 1rem;
        }
        .teacher-chatbot-welcome-icon {
            width: 4rem;
            height: 4rem;
            background-color: #dbeafe;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .teacher-chatbot-welcome-icon svg {
            width: 2rem;
            height: 2rem;
            color: #2563eb;
        }
        .teacher-chatbot-welcome h4 {
            font-size: 1.125rem;
            font-weight: 600;
            color: #111827;
            margin: 0 0 0.5rem 0;
        }
        .teacher-chatbot-welcome p {
            color: #4b5563;
            font-size: 0.875rem;
            margin: 0 0 1rem 0;
        }
        .teacher-chatbot-quick-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: center;
        }
        .teacher-chatbot-quick-btn {
            font-size: 0.75rem;
            background-color: #eff6ff;
            color: #2563eb;
            padding: 0.25rem 0.75rem;
            border-radius: 0;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background-color 0.15s;
        }
        .teacher-chatbot-quick-btn:hover {
            background-color: #dbeafe;
        }
        .teacher-chatbot-input-wrap {
            border-top: 1px solid #e5e7eb;
            padding: 1rem;
            background-color: #ffffff;
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }
        .teacher-chatbot-input-form {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .teacher-chatbot-input {
            flex: 1;
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0;
            font-size: 0.875rem;
        }
        .teacher-chatbot-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }
        .teacher-chatbot-send-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.5rem;
            background-color: #2563eb;
            color: #ffffff;
            border: 1px solid #2563eb;
            border-radius: 0;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.15s, border-color 0.15s;
        }
        .teacher-chatbot-send-btn:hover:not(:disabled) {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }
        .teacher-chatbot-send-btn:disabled {
            background-color: #9ca3af;
            border-color: #9ca3af;
            cursor: not-allowed;
        }
        .teacher-chatbot-send-btn svg {
            width: 1.25rem;
            height: 1.25rem;
        }
        .teacher-chatbot-msg {
            margin-bottom: 1rem;
        }
        .teacher-chatbot-msg--user {
            display: flex;
            justify-content: flex-end;
        }
        .teacher-chatbot-msg--bot {
            display: flex;
            justify-content: flex-start;
        }
        .teacher-chatbot-bubble {
            padding: 0.5rem 1rem;
            max-width: 80%;
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }
        .teacher-chatbot-bubble--user {
            background-color: #2563eb;
            color: #ffffff;
        }
        .teacher-chatbot-bubble--bot {
            background-color: #ffffff;
            color: #1f2937;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }
        .teacher-chatbot-bubble-time {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 0.25rem;
        }
        .teacher-chatbot-loading {
            display: flex;
            justify-content: flex-start;
        }
        .teacher-chatbot-loading-dots {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            background-color: #ffffff;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }
        .teacher-chatbot-dot {
            width: 0.5rem;
            height: 0.5rem;
            background-color: #9ca3af;
            border-radius: 50%;
        }

        /* Teacher dashboard: My Courses card, rows, thumbnail, placeholder */
        .teacher-dashboard-courses {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            border: 1px solid #f3f4f6;
            overflow: hidden;
        }
        .teacher-dashboard-courses-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .teacher-dashboard-courses-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1e3a5f;
            margin: 0;
        }
        @media (min-width: 1024px) {
            .teacher-dashboard-courses-title {
                font-size: 1.25rem;
            }
        }
        @media (min-width: 640px) {
            .teacher-dashboard-courses-header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        @media (min-width: 1024px) {
            .teacher-dashboard-courses-header {
                padding: 1.25rem 1.5rem;
            }
        }
        .teacher-dashboard-courses-viewall {
            font-size: 0.875rem;
            font-weight: 500;
            color: #2563eb;
            text-decoration: none;
            transition: color 0.15s;
        }
        .teacher-dashboard-courses-viewall:hover {
            color: #1d4ed8;
        }
        .teacher-dashboard-courses-viewall:focus {
            outline: none;
            text-decoration: underline;
        }
        .teacher-dashboard-courses-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
        }
        @media (min-width: 640px) {
            .teacher-dashboard-courses-list {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
                padding: 1rem 1.25rem;
            }
        }
        @media (min-width: 1024px) {
            .teacher-dashboard-courses-list {
                grid-template-columns: repeat(3, 1fr);
                gap: 1.25rem;
                padding: 1.25rem 1.5rem;
            }
        }
        .teacher-dashboard-course-row {
            display: flex;
            flex-direction: column;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            overflow: hidden;
            background-color: #ffffff;
            transition: box-shadow 0.15s, border-color 0.15s;
            height: 100%;
        }
        .teacher-dashboard-course-row:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border-color: #d1d5db;
        }
        .teacher-dashboard-course-row-inner {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .teacher-dashboard-course-thumb {
            flex-shrink: 0;
            display: block;
            width: 100%;
            height: 7rem;
            overflow: hidden;
            background: linear-gradient(to bottom right, #60a5fa, #4f46e5);
        }
        @media (min-width: 640px) {
            .teacher-dashboard-course-thumb {
                height: 8rem;
            }
        }
        .teacher-dashboard-course-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        .teacher-dashboard-course-thumb-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .teacher-dashboard-course-thumb-placeholder svg {
            width: 1.5rem;
            height: 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            flex-shrink: 0;
        }

        /* Teacher dashboard: course row content block, title, meta, status, actions */
        .teacher-dashboard-course-content {
            flex: 1;
            min-width: 0;
            padding: 0.75rem 1rem;
        }
        .teacher-dashboard-course-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #0f172a;
            text-decoration: none;
            display: inline-block;
        }
        .teacher-dashboard-course-title:hover {
            color: #2563eb;
        }
        .teacher-dashboard-course-title:focus {
            outline: none;
            text-decoration: underline;
        }
        .teacher-dashboard-course-subject {
            font-size: 0.8125rem;
            color: #64748b;
            margin-top: 0.125rem;
        }
        .teacher-dashboard-course-enrollments {
            font-size: 0.6875rem;
            color: #94a3b8;
            margin-top: 0.125rem;
        }
        .teacher-dashboard-course-meta {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .teacher-dashboard-course-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0;
            padding: 0.5rem 1rem;
            border-top: 1px solid #f3f4f6;
            margin-top: auto;
        }
        .teacher-dashboard-course-status {
            display: inline-flex;
            align-items: center;
            padding: 0.125rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.6875rem;
            font-weight: 500;
            width: fit-content;
        }
        .teacher-dashboard-course-status--success {
            background-color: #dcfce7;
            color: #166534;
        }
        .teacher-dashboard-course-status--warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        .teacher-dashboard-course-status--default {
            background-color: #f3f4f6;
            color: #374151;
        }
        .teacher-dashboard-course-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.375rem;
        }
        .teacher-dashboard-course-action {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #374151;
            background-color: #ffffff;
            text-decoration: none;
            transition: background-color 0.15s, border-color 0.15s;
        }
        .teacher-dashboard-course-action:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
            color: #374151;
        }
        .teacher-dashboard-course-action:focus {
            outline: none;
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px #9ca3af;
        }
        .teacher-dashboard-courses-empty {
            grid-column: 1 / -1;
        }
        .teacher-dashboard-empty-title {
            font-size: 1rem;
            font-weight: 600;
            color: #0f172a;
            margin: 0;
        }
        @media (min-width: 1024px) {
            .teacher-dashboard-empty-title {
                font-size: 1.125rem;
            }
        }
        .teacher-dashboard-empty-desc {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0.5rem 0 0 0;
        }

    </style>

    @stack('styles')
</head>
<body class="bg-gray-100" x-data="{ sidebarOpen: false, profileMenuOpen: false }">
    <div class="teacher-layout-root min-h-screen flex overflow-x-hidden">
        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="sidebar-overlay lg:hidden" x-cloak></div>

        <!-- Sidebar: fixed on all screens so it does not scroll with the page -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="teacher-layout-sidebar fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 text-white h-screen overflow-y-auto transform transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
            <div class="p-4">
                <div class="mb-8">
                    <!-- Close button (Mobile only) -->
                    <div class="lg:hidden flex justify-end mb-4">
                        <button @click="sidebarOpen = false" class="text-white hover:text-gray-300 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Logo and Name with Role (All Screens) -->
                    <div class="pb-4 border-b border-gray-700">
                        <!-- Logo -->
                        <a href="{{ route('teacher.dashboard') }}" class="flex justify-center mb-3 hover:opacity-80 transition">
                            <img src="{{ asset('logo.jpeg') }}" alt="Logo" class="h-12">
                        </a>
                        <!-- Name and Role -->
                        <div class="text-center">
                            <p class="text-white font-semibold text-sm">
                                {{ trim(Auth::user()->first_name . ' ' . Auth::user()->last_name) ?: Auth::user()->name }}
                            </p>
                            <p class="text-gray-400 text-xs mt-1">Teacher</p>
                        </div>
                    </div>
                </div>

                <nav class="space-y-2">
                    <a href="{{ route('teacher.dashboard') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('teacher.dashboard') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('teacher.courses.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('teacher.courses.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span>My Courses</span>
                    </a>
                    <a href="{{ route('teacher.students.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('teacher.students.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span>Students</span>
                    </a>
                    <a href="{{ route('teacher.chatbot.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('teacher.chatbot.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <span>Chatbot</span>
                    </a>
                    <a href="{{ route('teacher.devices.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('teacher.devices.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                        <span>Devices</span>
                    </a>
                    <a href="{{ route('teacher.profile.show') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('teacher.profile.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span>Profile</span>
                    </a>
                    <a href="{{ route('teacher.settings.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('teacher.settings.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>Settings</span>
                    </a>
                    <div class="border-t border-gray-700 my-2"></div>
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span>Main Website</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="mt-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center space-x-2 px-4 py-2 rounded-lg text-red-200 hover:text-white hover:bg-red-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </nav>
            </div>
        </aside>

        <!-- Main Content: on lg, ml-64 and max-w push content to the right of the fixed sidebar (never underneath it) -->
        <div class="teacher-layout-main flex-1 flex flex-col min-w-0 w-full lg:ml-64 lg:max-w-[calc(100%-16rem)]">
            <!-- Top Bar -->
            <header class="teacher-layout-header">
                <div class="teacher-layout-header-inner">
                    <div class="teacher-layout-header-left">
                        <!-- Mobile Menu Button -->
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 hover:text-gray-900 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h1 class="teacher-layout-header-title">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <!-- Profile Dropdown (All Screen Sizes) -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg p-1">
                            @if(Auth::user()->profile_image)
                                <img src="{{ Auth::user()->getProfileImageUrl() }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-gray-300">
                            @else
                                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center border-2 border-gray-300">
                                    <span class="text-white font-semibold text-sm">{{ Auth::user()->getInitials() }}</span>
                                </div>
                            @endif
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl py-1 z-50 border border-gray-200 overflow-hidden"
                             x-cloak>
                            <!-- User Info Section -->
                            <div class="px-4 py-4 bg-gradient-to-br from-gray-50 to-gray-100 border-b border-gray-200">
                                <div class="mb-3">
                                    <p class="text-base font-semibold text-gray-900 mb-1">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                </div>
                                @if(Auth::user()->last_login_at)
                                    <div class="mt-3 pt-3 border-t border-gray-200/60">
                                        <div class="flex items-start space-x-2">
                                            <svg class="w-4 h-4 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Last Login</p>
                                                <p class="text-sm text-gray-900 font-semibold">{{ Auth::user()->last_login_at->format('M d, Y') }}</p>
                                                <p class="text-xs text-gray-500">{{ Auth::user()->last_login_at->format('h:i A') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-3 pt-3 border-t border-gray-200/60">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">First Login</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <!-- Menu Items -->
                            <div class="flex border-t border-gray-100 py-1">
                                <a href="{{ route('teacher.profile.edit') }}" class="flex-1 flex items-center justify-center space-x-1.5 px-4 py-3 mx-1 my-1 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150 border-r border-gray-200 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="font-medium text-xs">Edit Profile</span>
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="flex items-center justify-center space-x-1.5 w-full px-4 py-3 mx-1 my-1 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150 rounded-lg">
                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        <span class="font-medium text-xs">Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content: pt-16 reserves space for the fixed header (64px) so content does not sit underneath -->
            <main class="teacher-layout-content flex-1 px-4 pb-4 lg:px-6 lg:pb-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <!-- Toast Notifications -->
    @include('components.notification-toast')
</body>
</html>


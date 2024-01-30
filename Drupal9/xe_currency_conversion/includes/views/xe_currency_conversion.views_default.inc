<?php

/**
 * @file
 * Provides default views for xe currency conversion table.
 */

use Drupal\views\Entity\Display;
use Drupal\views\Entity\View;

/**
 * Implements hook_views_default_views().
 */
function xe_currency_conversion_views_default_views() {
  $views = [];

  $view = new View([
    "id" => "xe_currencies",
    "description" => "",
    "tag" => "default",
    "base_table" => "xe_currency_conversion",
    "name" => "Xe Currencies",
    "core" => 8,
    "api_version" => "3.0",
    "disabled" => FALSE,
  ]);

  $display = new Display([
    "id" => "default",
    "display_title" => "Master",
    "display_plugin" => "default",
    "display_options" => [
      "use_more_always" => FALSE,
      "access" => [
        "type" => "none",
      ],
      "cache" => [
        "type" => "none",
      ],
      "query" => [
        "type" => "views_query",
      ],
      "exposed_form" => [
        "type" => "basic",
      ],
      "pager" => [
        "type" => "full",
        "options" => [
          "items_per_page" => "20",
          "offset" => "0",
          "id" => "0",
          "quantity" => "9",
        ],
      ],
      "style_plugin" => "table",
      "style_options" => [
        "columns" => [
          "symbol" => "symbol",
          "name" => "name",
          "rate" => "rate",
          "inverse" => "inverse",
        ],
        "default" => "-1",
        "info" => [
          "symbol" => [
            "sortable" => 1,
            "default_sort_order" => "asc",
            "align" => "",
            "separator" => "",
            "empty_column" => 0,
          ],
          "name" => [
            "sortable" => 1,
            "default_sort_order" => "asc",
            "align" => "",
            "separator" => "",
            "empty_column" => 0,
          ],
          "rate" => [
            "sortable" => 1,
            "default_sort_order" => "asc",
            "align" => "",
            "separator" => "",
            "empty_column" => 0,
          ],
          "inverse" => [
            "sortable" => 1,
            "default_sort_order" => "asc",
            "align" => "",
            "separator" => "",
            "empty_column" => 0,
          ],
        ],
      ],
    ],
  ]);

  $handler = $view->new_display($display);
  $handler->display_options["use_more_always"] = FALSE;
  $handler->display_options["access"]["type"] = "none";
  $handler->display_options["cache"]["type"] = "none";
  $handler->display_options["query"]["type"] = "views_query";
  $handler->display_options["exposed_form"]["type"] = "basic";
  $handler->display_options["pager"]["type"] = "full";
  $handler->display_options["pager"]["options"]["items_per_page"] = "20";
  $handler->display_options["pager"]["options"]["offset"] = "0";
  $handler->display_options["pager"]["options"]["id"] = "0";
  $handler->display_options["pager"]["options"]["quantity"] = "9";
  $handler->display_options["style_plugin"] = "table";
  $handler->display_options["style_options"]["columns"] = [
    "symbol" => "symbol",
    "name" => "name",
    "rate" => "rate",
    "inverse" => "inverse",
  ];
  $handler->display_options["style_options"]["default"] = "-1";
  $handler->display_options["style_options"]["info"] = [
    "symbol" => [
      "sortable" => 1,
      "default_sort_order" => "asc",
      "align" => "",
      "separator" => "",
      "empty_column" => 0,
    ],
    "name" => [
      "sortable" => 1,
      "default_sort_order" => "asc",
      "align" => "",
      "separator" => "",
      "empty_column" => 0,
    ],
    "rate" => [
      "sortable" => 1,
      "default_sort_order" => "asc",
      "align" => "",
      "separator" => "",
      "empty_column" => 0,
    ],
    "inverse" => [
      "sortable" => 1,
      "default_sort_order" => "asc",
      "align" => "",
      "separator" => "",
      "empty_column" => 0,
    ],
  ];

  $handler->override_option("fields", [
    "symbol" => [
      "id" => "symbol",
      "table" => "xe_currency_conversion",
      "field" => "symbol",
      "label" => "Code",
    ],
    "name" => [
      "id" => "name",
      "table" => "xe_currency_conversion",
      "field" => "name",
    ],
    "rate" => [
      "id" => "rate",
      "table" => "xe_currency_conversion",
      "field" => "rate",
      "label" => "Rate (Against GBP)",
    ],
    "inverse" => [
      "id" => "inverse",
      "table" => "xe_currency_conversion",
      "field" => "inverse",
    ],
  ]);

  $handler->override_option("filters", [
    "symbol" => [
      "id" => "symbol",
      "table" => "xe_currency_conversion",
      "field" => "symbol",
      "operator" => "contains",
      "exposed" => TRUE,
      "expose" => [
        "operator_id" => "symbol_op",
        "label" => "Code",
        "operator" => "symbol_op",
        "identifier" => "symbol",
        "remember_roles" => [
          2 => "2",
          1 => 0,
          3 => 0,
          4 => 0,
        ],
      ],
    ],
    "name" => [
      "id" => "name",
      "table" => "xe_currency_conversion",
      "field" => "name",
      "operator" => "contains",
      "exposed" => TRUE,
      "expose" => [
        "operator_id" => "name_op",
        "label" => "Name",
        "operator" => "name_op",
        "identifier" => "name",
        "remember_roles" => [
          2 => "2",
          1 => 0,
          3 => 0,
          4 => 0,
        ],
      ],
    ],
  ]);

  $display = new Display([
    "id" => "page",
    "display_title" => "Page",
    "display_plugin" => "page",
    "display_options" => [
      "title" => "Xe Currencies",
      "access" => [
        "type" => "perm",
        "perm" => "administer xe currency conversion",
      ],
      "path" => "admin/config/services/xe_currency_conversion/currencies",
      "menu" => [
        "type" => "tab",
        "title" => "Currencies",
        "description" =>
        "View the latest version of XE Currency rates.",
        "weight" => "40",
        "context" => 0,
        "context_only_inline" => 0,
      ],
    ],
  ]);

  $handler = $view->new_display($display);
  $handler->display_options["defaults"]["title"] = FALSE;
  $handler->display_options["title"] = "Xe Currencies";
  $handler->display_options["defaults"]["access"] = FALSE;
  $handler->display_options["access"]["type"] = "perm";
  $handler->display_options["access"]["perm"] =
        "administer xe currency conversion";
  $handler->display_options["path"] =
        "admin/config/services/xe_currency_conversion/currencies";
  $handler->display_options["menu"]["type"] = "tab";
  $handler->display_options["menu"]["title"] = "Currencies";
  $handler->display_options["menu"]["description"] =
        "View the latest version of XE Currency rates.";
  $handler->display_options["menu"]["weight"] = "40";
  $handler->display_options["menu"]["context"] = 0;
  $handler->display_options["menu"]["context_only_inline"] = 0;

  $translatables["xe_currencies"] = [
    "Master",
    "more",
    "Apply",
    "Reset",
    "Sort by",
    "Asc",
    "Desc",
    "Items per page",
    "- All -",
    "Offset",
    "« first",
    "‹ previous",
    "next ›",
    "last »",
    "Code",
    "Name",
    "Rate (Against GBP)",
    ".",
    ",",
    "Inverse rate",
    "Page",
    "Xe Currencies",
  ];

  $views[$view->id()] = $view;

  return $views;
}

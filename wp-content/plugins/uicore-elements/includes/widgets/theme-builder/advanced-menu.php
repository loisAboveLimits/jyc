<?php

namespace UiCoreElements;

use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Settings;

use Elementor\Modules\NestedElements\Controls\Control_Nested_Repeater;

use Elementor\Repeater;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

use Elementor\Icons_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Typography;

defined('ABSPATH') || exit();

/**
 * Advanced Menu widget.
 *
 * @since 1.3.15
 */

class Advanced_Menu extends UiCoreNestedWidget
{

    public function get_name()
    {
        return 'uicore-advanced-menu';
    }
    public function get_title()
    {
        return __('Advanced Menu', 'uicore-elements');
    }
    public function get_icon()
    {
        return 'eicon-mega-menu ui-e-widget';
    }
    public function get_keywords()
    {
        return ['menu', 'navigation'];
    }
    public function get_categories()
    {
        return ['uicore'];
    }
    public function get_styles()
    {
        $styles = [
            'advanced-menu',
            'advanced-menu-editor' => [
                'custom_condition' => $this->is_edit_mode()
            ],
            'advanced-menu-fullscreen-layout' => [
                'condition' => [
                    'breakpoint_selector!' => 'none'
                ]
            ],
            'advanced-menu-website-blur' => [
                'condition' => [
                    'dropdown_animation' => 'blur',
                ]
            ],
            'advanced-menu-canvas-dropdown-expand' => [
                'condition' => [
                    'fullscreen_canvas_dropdown' => 'expand'
                ]
            ],
            'advanced-menu-canvas-dropdown-slide' => [
                'condition' => [
                    'fullscreen_canvas_dropdown' => 'slide'
                ]
            ],
        ];

        $hover_animations = ['underline', 'button', 'magnet', 'focus', 'text_flip'];

        foreach ($hover_animations as $animation) {
            $styles["advanced-menu-hover-$animation"] = [
                'condition' => [
                    'hover_interaction' => $animation,
                ]
            ];
        }

        return $styles;
    }
    public function get_scripts()
    {
        return [
            'advanced-menu',
        ];
    }
    public function has_widget_inner_wrapper(): bool
    {
        // TODO: remove after Optmized Markup experiment is merged to the core
        return ! \Elementor\Plugin::$instance->experiments->is_feature_active('e_optimized_markup');
    }

    protected function get_default_children_elements()
    {
        return [
            [
                'elType' => 'container',
                'settings' => [
                    '_title' => __('Home', 'uicore-elements'),
                ],
            ],
            [
                'elType' => 'container',
                'settings' => [
                    '_title' => __('Services', 'uicore-elements'),
                ],
            ],
            [
                'elType' => 'container',
                'settings' => [
                    '_title' => __('Contact', 'uicore-elements'),
                ],
            ],
            [
                'elType' => 'container',
                'settings' => [
                    '_title' => __('Fullscreen Menu Widget', 'uicore-elements'),
                ],
            ],
        ];
    }

    protected function get_default_repeater_title_setting_key()
    {
        return 'item_title';
    }

    protected function get_default_children_title()
    {
        /* translators: %d: Menu item index */
        return esc_html__('Item #%d', 'uicore-elements');
    }

    protected function get_default_children_placeholder_selector()
    {
        return '.ui-e-menu-list';
    }

    protected function get_default_children_container_placeholder_selector()
    {
        return '.ui-e-menu-item';
    }

    protected function register_controls()
    {
        if (!Plugin::$instance->experiments->is_feature_active('nested-elements')) {
            $this->nesting_fallback('controls');
            return;
        }

        if (!Plugin::$instance->experiments->is_feature_active('e_optimized_markup')) {
            $this->start_controls_section(
                'section_tabs',
                [
                    'label' => esc_html__('Tabs', 'uicore-elements'),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_control(
                'tabs_alert',
                [
                    'type' => \Elementor\Controls_Manager::ALERT,
                    'alert_type' => 'danger',
                    'heading' => esc_html__('Feature Disabled.', 'uicore-elements'),
                    'content' => sprintf(
                        /* translators: 1: opening url tags 2: closing url tag */
                        __('Please, %1$s click here %2$s and enable the Optimized Markup experiment in Elementor settings', 'uicore-elements'),
                        '<a href="' . esc_attr(Settings::get_settings_tab_url('experiments')) . '" target="_blank">',
                        '</a>'
                    )
                ]
            );

            $this->end_controls_section();

            return;
        }

        $start_logic = is_rtl() ? 'end' : 'start';
        $end_logic = is_rtl() ? 'start' : 'end';

        $list = '{{WRAPPER}} > nav > .ui-e-menu > .ui-e-menu-list';
        $item = $list . ' > .ui-e-menu-item';
        $link = $item . ' > a';
        $default_nav = '{{WRAPPER}}:not(.ui-e-fullscreen-mode) > nav';

        $fullscreen_canvas = '{{WRAPPER}}.ui-e-fullscreen-mode  > nav';

        $this->start_controls_section(
            'section_layout',
            [
                'label' => esc_html__('Layout', 'uicore-elements'),
            ]
        );

        $this->add_control(
            'menu_name',
            [
                'label' => esc_html__('Menu Name', 'uicore-elements'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Menu', 'uicore-elements'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'item_convert_to_widget',
            [
                'label' => esc_html__('Convert to Widget', 'uicore-elements'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'render_type' => 'template',
            ]
        );

        $repeater->add_control(
            'widget_area_alert',
            [
                'type' => Controls_Manager::ALERT,
                'alert_type' => 'info',
                'content' => esc_html__('You have enabled the Fullscreen Menu Widget area through this option. Keep one per menu. To customize it, switch to the device where your default menu transforms into the fullscreen menu, and open it.', 'uicore-elements'),
                'condition' => [
                    'item_convert_to_widget' => 'yes',
                ],
            ]
        );

        $repeater->add_control(
            'item_title',
            [
                'label' => esc_html__('Title', 'uicore-elements'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Item Title', 'uicore-elements'),
                'dynamic' => [
                    'active' => true,
                ],
                'render_type' => 'template',
                'label_block' => true,
                'condition' => [
                    'item_convert_to_widget!' => 'yes',
                ],
            ]
        );

        $repeater->add_control(
            'item_link',
            [
                'label' => esc_html__('Link', 'uicore-elements'),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__('Paste URL or type', 'uicore-elements'),
                'dynamic' => [
                    'active' => true,
                ],
                'render_type' => 'template',
                'condition' => [
                    'item_convert_to_widget!' => 'yes',
                ],
            ]
        );

        $repeater->add_control(
            'item_submenu',
            [
                'label' => esc_html__('Has submenu', 'uicore-elements'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'render_type' => 'template',
                'condition' => [
                    'item_convert_to_widget!' => 'yes',
                ],
            ]
        );

        $repeater->add_control(
            'item_submenu_width',
            [
                'label' => esc_html__('Submenu Alignment', 'uicore-elements'),
                'type' => Controls_Manager::SELECT,
                'default' => 'item-width',
                'options' => [
                    'widget-width' => esc_html__('Widget', 'uicore-elements'),
                    'item-width' => esc_html__('Menu Item', 'uicore-elements'),
                    'full-width' => esc_html__('Full Width', 'uicore-elements')
                ],
                'description' => esc_html__('Set the width and container placement of this item submenu container.', 'uicore-elements'),
                'render_type' => 'template',
                'condition' => [
                    'item_submenu' => 'yes',
                    'item_convert_to_widget!' => 'yes',
                ],
            ]
        );

        $repeater->add_control(
            'item_icon',
            [
                'label' => esc_html__('Icon', 'uicore-elements'),
                'type' => Controls_Manager::ICONS,
                'skin' => 'inline',
                'render_type' => 'template',
                'label_block' => false,
                'condition' => [
                    'item_convert_to_widget!' => 'yes',
                ],
            ]
        );

        $repeater->add_control(
            'item_image',
            [
                'label' => esc_html__('Image', 'uicore-elements'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'render_type' => 'template',
                'label_block' => true,
                'condition' => [
                    'item_convert_to_widget!' => 'yes',
                ],
            ]
        );

        $repeater->add_control(
            'item_badge',
            [
                'label' => esc_html__('Badge', 'uicore-elements'),
                'type' => Controls_Manager::TEXT,
                'render_type' => 'template',
                'label_block' => true,
                'condition' => [
                    'item_convert_to_widget!' => 'yes',
                ],
            ]
        );

        $repeater->add_control(
            'item_description',
            [
                'label' => esc_html__('Description', 'uicore-elements'),
                'type' => Controls_Manager::TEXT,
                'render_type' => 'template',
                'label_block' => true,
                'condition' => [
                    'item_convert_to_widget!' => 'yes',
                ],
            ]
        );

        $repeater->add_control(
            'element_id',
            [
                'label' => esc_html__('CSS ID', 'uicore-elements'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'style_transfer' => false,
                'condition' => [
                    'item_convert_to_widget!' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'menu_items',
            [
                'label' => esc_html__('Menu Items', 'uicore-elements'),
                'type' => Control_Nested_Repeater::CONTROL_TYPE,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'item_title' => esc_html__('Home', 'uicore-elements'),
                    ],
                    [
                        'item_title' => esc_html__('Services', 'uicore-elements'),
                        'item_submenu' => 'yes',
                    ],
                    [
                        'item_title' => esc_html__('Contact', 'uicore-elements'),
                    ],
                    [
                        'item_convert_to_widget' => 'yes',
                    ],
                ],
                'title_field' => '{{{ item_convert_to_widget === "yes" ? "Fullscreen Menu Widget" : item_title }}}',
            ]
        );

        $this->add_control(
            'submenu_trigger',
            [
                'label' => esc_html__('Sub menu trigger', 'uicore-elements'),
                'type' => Controls_Manager::SELECT,
                'default' => 'hover',
                'options' => [
                    'hover' => esc_html__('Hover', 'uicore-elements'),
                    'click' => esc_html__('Click', 'uicore-elements'),
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'highlight_current',
            [
                'label' => esc_html__('Highlight Current Item', 'uicore-elements'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'highlight_warning',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => esc_html__('To change the current item highlight styles, go to Style tab > Menu Items group > Active tab.', 'uicore-elements'),
                'content_classes' => 'elementor-control-field-description',
                'condition' => [
                    'highlight_current' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section('default_menu_section', [
            'label' => esc_html__('Default Menu', 'uicore-elements'),
        ]);

        $this->add_control(
            'item_layout',
            [
                'label' => esc_html__('Item Layout', 'uicore-elements'),
                'type' => Controls_Manager::SELECT,
                'default' => 'horizontal',
                'options' => [
                    'horizontal' => esc_html__('Horizontal', 'uicore-elements'),
                    'vertical' => esc_html__('Vertical', 'uicore-elements'),
                ],
                'prefix_class' => 'ui-e-layout-',
                'frontend_available' => true,
            ]
        );

        $this->add_responsive_control(
            'item_align',
            [
                'label' => esc_html__('Items Alignment', 'uicore-elements'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => esc_html__('Start', 'uicore-elements'),
                        'icon' => "eicon-align-$start_logic-h",
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'uicore-elements'),
                        'icon' => 'eicon-align-center-h',
                    ],
                    'end' => [
                        'title' => esc_html__('End', 'uicore-elements'),
                        'icon' => "eicon-align-$end_logic-h",
                    ],
                    'space-between' => [
                        'title' => esc_html__('Space Between', 'uicore-elements'),
                        'icon' => 'eicon-justify-space-between-h',
                    ],
                    'space-around' => [
                        'title' => esc_html__('Space Around', 'uicore-elements'),
                        'icon' => 'eicon-justify-space-around-h',
                    ],
                    'space-evenly' => [
                        'title' => esc_html__('Space Evenly', 'uicore-elements'),
                        'icon' => 'eicon-justify-space-evenly-h',
                    ],
                ],
                'label_block' => true,
                'selectors' => [
                    $list => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'item_layout' => 'horizontal',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_vertical_align',
            [
                'label' => esc_html__('Items Alignment', 'uicore-elements'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => esc_html__('Start', 'uicore-elements'),
                        'icon' => "eicon-align-$start_logic-h",
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'uicore-elements'),
                        'icon' => 'eicon-align-center-h',
                    ],
                    'end' => [
                        'title' => esc_html__('End', 'uicore-elements'),
                        'icon' => "eicon-align-$end_logic-h",
                    ],
                ],
                'selectors' => [
                    $link => 'justify-content: {{VALUE}};'
                ],
                'condition' => [
                    'item_layout' => 'vertical',
                ],
            ]
        );

        $this->add_control(
            'dropdown_icon',
            [
                'label' => esc_html__('Dropdown Icon', 'uicore-elements'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-caret-down',
                    'library' => 'fa-solid',
                ],
                'skin' => 'inline',
                'label_block' => false,
            ]
        );

        $this->add_control(
            'dropdown_icon_active',
            [
                'label' => esc_html__('Dropdown Icon Active', 'uicore-elements'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-caret-up',
                    'library' => 'fa-solid',
                ],
                'skin' => 'inline',
                'label_block' => false,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section('fullscreen_menu_section', [
            'label' => esc_html__('Fullscreen Layout', 'uicore-elements'),
        ]);

        // Build breakpoint options
        $dropdown_options = [
            'none' => esc_html__('None', 'uicore-elements'),
        ];
        $excluded_breakpoints = [
            'laptop',
            'tablet_extra',
            'widescreen',
        ];

        foreach (Plugin::$instance->breakpoints->get_active_breakpoints() as $breakpoint_key => $breakpoint_instance) {
            // Exclude the larger breakpoints from the dropdown selector.
            if (in_array($breakpoint_key, $excluded_breakpoints, true)) {
                continue;
            }

            $dropdown_options[$breakpoint_key] = sprintf(
                /* translators: 1: Breakpoint label, 2: `>` character, 3: Breakpoint value. */
                esc_html__('%1$s (%2$s %3$dpx)', 'uicore-elements'),
                $breakpoint_instance->get_label(),
                '>',
                $breakpoint_instance->get_value()
            );
        }

        $dropdown_options['desktop'] = esc_html__('Desktop', 'uicore-elements');

        $this->add_control(
            'breakpoint_selector',
            [
                'label' => esc_html__('Breakpoint', 'uicore-elements'),
                'type' => Controls_Manager::SELECT,
                'options' => $dropdown_options,
                'default' => 'tablet',
                'prefix_class' => 'ui-e-menu-',
                'render_type' => 'template',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'button_style',
            [
                'label' => esc_html__('Button Layout', 'uicore-elements'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'default' => esc_html__('Default', 'uicore-elements'),
                    'classic' => esc_html__('Classic', 'uicore-elements'),
                    'minimalist' => esc_html__('Minimalist', 'uicore-elements'),
                    'creative' => esc_html__('Creative', 'uicore-elements'),
                    'text' => esc_html__('Text', 'uicore-elements'),
                    'custom' => esc_html__('Custom', 'uicore-elements'),
                ],
                'render_type' => 'template',
                'description' => esc_html__('To enable the custom icon selectors, you need to set "Custom" as option.', 'uicore-elements'),
                'default' => 'default',
                'prefix_class' => 'ui-e-fullscreen-button-icon-',
                'frontend_available' => true,
                'condition' => [
                    'breakpoint_selector!' => 'none'
                ]
            ]
        );

        $this->add_control(
            'button_text_open',
            [
                'label' => esc_html__('Open Text', 'uicore-elements'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Menu', 'uicore-elements'),
                'render_type' => 'template',
                'condition' => [
                    'button_style' => 'text',
                    'breakpoint_selector!' => 'none'
                ],
            ]
        );

        $this->add_control(
            'button_text_close',
            [
                'label' => esc_html__('Close Text', 'uicore-elements'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Close', 'uicore-elements'),
                'render_type' => 'template',
                'condition' => [
                    'button_style' => 'text',
                    'breakpoint_selector!' => 'none'
                ],
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'fullscreen_menu_open_icon',
            [
                'label' => esc_html__('Open Button Icon', 'uicore-elements'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-bars',
                    'library' => 'fa-solid',
                ],
                'skin' => 'inline',
                'label_block' => false,
                'condition' => [
                    'button_style' => 'custom',
                    'breakpoint_selector!' => 'none'
                ]
            ]
        );

        $this->add_control(
            'fullscreen_menu_close_icon',
            [
                'label' => esc_html__('Close Icon', 'uicore-elements'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-window-close',
                    'library' => 'fa-solid',
                ],
                'skin' => 'inline',
                'label_block' => false,
                'condition' => [
                    'button_style' => 'custom',
                    'breakpoint_selector!' => 'none'
                ],
                'separator' => 'after'
            ]
        );

        $this->add_control(
            'dropdown_fullscreen_icon',
            [
                'label' => esc_html__('Dropdown Icon', 'uicore-elements'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-caret-right',
                    'library' => 'fa-solid',
                ],
                'skin' => 'inline',
                'label_block' => false,
            ]
        );

        $this->add_control(
            'dropdown_fullscreen_icon_active',
            [
                'label' => esc_html__('Dropdown Icon Active', 'uicore-elements'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-caret-left',
                    'library' => 'fa-solid',
                ],
                'skin' => 'inline',
                'label_block' => false,
            ]
        );

        $this->add_control(
            'widget_area_only',
            [
                'label' => esc_html__('Widget Area Only', 'uicore-elements'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'render_type' => 'template',
                'description' => esc_html__('When enabled, the fullscreen menu will only display the content of the assigned widget area, hiding menu items. To use this option, you need to convert one of your menu items to widget.', 'uicore-elements'),
                'condition' => [
                    'breakpoint_selector!' => 'none'
                ],
            ]
        );

        $this->add_control(
            'canvas_separator',
            [
                'label' => esc_html__('Canvas', 'uicore-elements'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'breakpoint_selector!' => 'none'
                ]
            ]
        );

        $this->add_control(
            'fullscreen_canvas_items_alignment',
            [
                'label' => esc_html__('Items alignment', 'uicore-elements'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => esc_html__('Start', 'uicore-elements'),
                        'icon' => "eicon-align-$start_logic-h",
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'uicore-elements'),
                        'icon' => 'eicon-align-center-h',
                    ],
                    'end' => [
                        'title' => esc_html__('End', 'uicore-elements'),
                        'icon' => "eicon-align-$end_logic-h",
                    ],
                ],
                'default' => 'end',
                'toggle' => false,
                'selectors' => [
                    '{{WRAPPER}}.ui-e-fullscreen-mode > nav > .ui-e-menu > .ui-e-menu-list > .ui-e-menu-item > a' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'breakpoint_selector!' => 'none'
                ]
            ]
        );

        $this->add_control(
            'fullscreen_canvas_container_alignment',
            [
                'label' => esc_html__('Container Alignment', 'uicore-elements'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => esc_html__('Left', 'uicore-elements'),
                        'icon' => "eicon-arrow-left",
                    ],
                    'end' => [
                        'title' => esc_html__('Right', 'uicore-elements'),
                        'icon' => 'eicon-arrow-right',
                    ],
                ],
                'default' => 'end',
                'selectors_dictionary' => [
                    'start' => 'start; --ui-e-fullscreen-canvas-offset-x: -100%; --ui-e-fullscreen-canvas-panels-shift-x: -150%; --ui-e-fullscreen-canvas-submenu-shift-x: 100%; --ui-e-fullscreen-canvas-submenu-left: 50%; --ui-e-fullscreen-canvas-submenu-right: auto;',
                    'end' => 'end; --ui-e-fullscreen-canvas-offset-x: 100%; --ui-e-fullscreen-canvas-panels-shift-x: 150%; --ui-e-fullscreen-canvas-submenu-shift-x: -100%; --ui-e-fullscreen-canvas-submenu-left: auto; --ui-e-fullscreen-canvas-submenu-right: 50%;',
                ],
                'toggle' => false,
                'selectors' => [
                    $fullscreen_canvas => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'breakpoint_selector!' => 'none'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section('section_animations', [
            'label' => esc_html__('Animations', 'uicore-elements'),
        ]);

        $this->add_control(
            'default_animations',
            [
                'label' => esc_html__('Default Menu', 'uicore-elements'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'hover_interaction',
            [
                'label' => esc_html__('Hover Interaction', 'uicore-elements'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__('None', 'uicore-elements'),
                    'underline' => esc_html__('Underline', 'uicore-elements'),
                    'button' => esc_html__('Button', 'uicore-elements'),
                    'text_flip' => esc_html__('Text Flip', 'uicore-elements'),
                    'focus' => esc_html__('Focus', 'uicore-elements'),
                    'magnet' => esc_html__('Magnet Button', 'uicore-elements'),
                ],
                'render_type' => 'template',
                'prefix_class' => 'ui-e-hover-',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'hover_interaction_button_notice',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => esc_html__('To update the hover interaction colors, go to the Style tab > Menu Items section > Normal tab controls.', 'uicore-elements'),
                'content_classes' => 'elementor-control-field-description',
                'condition' => [
                    'hover_interaction' => ['button', 'magnet'],
                ],
            ]
        );

        $this->add_control(
            'interaction_color',
            [
                'label' => esc_html__('Interaction Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--ui-e-hover-interaction-color: {{VALUE}};',
                ],
                'condition' => [
                    'hover_interaction' => 'underline',
                ],
            ]
        );

        $this->add_control(
            'dropdown_animation',
            [
                'label' => esc_html__('Dropdown Animation', 'uicore-elements'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__('None', 'uicore-elements'),
                    'fade' => esc_html__('Fade', 'uicore-elements'),
                    'down' => esc_html__('Fade Down', 'uicore-elements'),
                    'up' => esc_html__('Fade Up', 'uicore-elements'),
                    'left' => esc_html__('Fade Left', 'uicore-elements'),
                    'scale' => esc_html__('Scale', 'uicore-elements'),
                    // 'scale_bg' => esc_html__('Scale Background', 'uicore-elements'),
                    'blur' => esc_html__('Website Blur', 'uicore-elements'),
                ],
                'render_type' => 'template',
                'prefix_class' => 'ui-e-dropdown-animation-',
            ]
        );

        $this->add_control(
            'blur_target_selector',
            [
                'label' => esc_html__('Blur Target Selector', 'uicore-elements'),
                'type' => Controls_Manager::TEXT,
                'default' => '#content',
                'placeholder' => '#content',
                'description' => esc_html__('HTML selector for the element(s) that should be blurred when a submenu is active. Separate multiple selectors with a comma.', 'uicore-elements'),
                'condition' => [
                    'dropdown_animation' => 'blur',
                ],
                'frontend_available' => true,
            ]
        );

        // Scale bg control
        // $this->add_control(
        //     'menu_spacing',
        //     [
        //         'label' => esc_html__('Bottom Spacing', 'uicore-elements'),
        //         'type' => \Elementor\Controls_Manager::SLIDER,
        //         'size_units' => ['px', 'em', 'rem', 'custom'],
        //         'range' => [
        //             'px' => [
        //                 'min' => 0,
        //                 'max' => 500,
        //                 'step' => 1,
        //             ],
        //         ],
        //         'default' => [
        //             'unit' => 'px',
        //             'size' => 50,
        //         ],
        //         'selectors' => [
        //             '{{WRAPPER}}' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        //         ],
        //         'condition' => [
        //             'dropdown_animation' => 'scale_bg',
        //         ]
        //     ]
        // );

        $this->add_control(
            'fullscreen_animation',
            [
                'label' => esc_html__('Fullscreen Menu', 'uicore-elements'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'breakpoint_selector!' => 'none'
                ]
            ]
        );

        $this->add_control(
            'fullscreen_canvas_animation',
            [
                'label' => esc_html__('Entrance Animation', 'uicore-elements'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__('Fade', 'uicore-elements'),
                    'ui-e-fullscreen-canvas-animation-slide-along' => esc_html__('Slide Along', 'uicore-elements'),
                    'ui-e-fullscreen-canvas-animation-slide-top' => esc_html__('Slide on Top', 'uicore-elements'),
                    'ui-e-fullscreen-canvas-animation-expand' => esc_html__('Expand', 'uicore-elements'),
                ],
                'prefix_class' => '',
                'condition' => [
                    'breakpoint_selector!' => 'none'
                ]
            ]
        );

        $this->add_control(
            'fullscreen_canvas_dropdown',
            [
                'label' => esc_html__('Dropdown Animation', 'uicore-elements'),
                'type' => Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'expand' => esc_html__('Expand', 'uicore-elements'),
                    'slide' => esc_html__('Slide', 'uicore-elements')
                ],
                'prefix_class' => 'ui-e-fullscreen-canvas-dropdown-animation-',
                'condition' => [
                    'breakpoint_selector!' => 'none'
                ]
            ]
        );

        $this->add_control(
            'back_button_text',
            [
                'label' => esc_html__('"Back" Button Text', 'uicore-elements'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Back', 'uicore-elements'),
                'condition' => [
                    'fullscreen_canvas_dropdown' => 'slide',
                    'breakpoint_selector!' => 'none'
                ],
                'frontend_available' => true
            ]
        );

        $this->add_control(
            'back_button_icon',
            [
                'label' => esc_html__('Icon', 'uicore-elements'),
                'type' => Controls_Manager::ICONS,
                'skin' => 'inline',
                'render_type' => 'template',
                'label_block' => false,
                'condition' => [
                    'fullscreen_canvas_dropdown' => 'slide',
                    'breakpoint_selector!' => 'none'
                ],
                'frontend_available' => true,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_menu_style',
            [
                'label' => esc_html__('Navigation', 'uicore-elements'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'menu_background',
                'selector' => $default_nav,
            ]
        );

        $this->add_responsive_control(
            'menu_padding',
            [
                'label' => esc_html__('Padding', 'uicore-elements'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'default' => [
                    'unit' => 'px',
                    'top' => 10,
                    'right' => 10,
                    'bottom' => 10,
                    'left' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ui-e-menu-padding-top: {{TOP}}{{UNIT}}; --ui-e-menu-padding-right: {{RIGHT}}{{UNIT}}; --ui-e-menu-padding-bottom: {{BOTTOM}}{{UNIT}}; --ui-e-menu-padding-left: {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} > nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'menu_radius',
            [
                'label' => esc_html__('Border Radius', 'uicore-elements'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'selectors' => [
                    $default_nav => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'menu_border',
                'selector' => $default_nav,
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'menu_box_shadow',
                'selector' => $default_nav,
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_menu_items_style',
            [
                'label' => esc_html__('Menu Items', 'uicore-elements'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'menu_item_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
                'selector' => $link . ' > .ui-e-text',
            ]
        );

        $this->add_responsive_control(
            'menu_items_gap',
            [
                'label' => esc_html__('Items gap', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => [
                        'max' => 150,
                    ],
                    'em' => [
                        'max' => 20,
                    ],
                    'rem' => [
                        'max' => 20,
                    ],
                ],
                'default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    $list => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'menu_item_padding',
            [
                'label' => esc_html__('Padding', 'uicore-elements'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'selectors' => [
                    $link => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'menu_item_border_radius',
            [
                'label' => esc_html__('Border Radius', 'uicore-elements'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'selectors' => [
                    '{{WRAPPER}}' => '--ui-e-menu-item-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('menu_items_style_tabs');

        $this->start_controls_tab(
            'menu_items_style_tab_normal',
            [
                'label' => esc_html__('Normal', 'uicore-elements'),
            ]
        );

        $this->add_responsive_control(
            'menu_item_color',
            [
                'label' => esc_html__('Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    $link => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'menu_item_background_color',
            [
                'label' => esc_html__('Background Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0)',
                'selectors' => [
                    '{{WRAPPER}}' => '--ui-e-menu-item-background: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'menu_item_box_shadow',
                'selector' => $link . ', ' . $list . ':before',
            ]
        );

        $this->end_controls_tab();

        $animations_exceptions = [
            'terms' => [
                [
                    'name' => 'hover_interaction',
                    'operator' => '!in',
                    'value' => ['magnet', 'button'],
                ],
            ],
        ];

        $this->start_controls_tab(
            'menu_items_style_tab_hover',
            [
                'label' => esc_html__('Hover', 'uicore-elements'),
            ]
        );

        $this->add_responsive_control(
            'menu_item_color_hover',
            [
                'label' => esc_html__('Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    $item . ' > a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'menu_item_background_color_hover',
            [
                'label' => esc_html__('Background Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    $item . ' > a:hover' => 'background-color: {{VALUE}};',
                ],
                'conditions' => $animations_exceptions
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'menu_item_box_shadow_hover',
                'selector' => $link . ':hover',
                'conditions' => $animations_exceptions
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'menu_items_style_tab_active',
            [
                'label' => esc_html__('Active', 'uicore-elements'),
            ]
        );

        $this->add_responsive_control(
            'menu_item_color_active',
            [
                'label' => esc_html__('Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    $item . '.ui-e-open > a,' . $item . '.ui-e-current > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'menu_item_background_color_active',
            [
                'label' => esc_html__('Background Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    $item . '.ui-e-open > a,' . $item . '.ui-e-current > a' => 'background-color: {{VALUE}};',
                ],
                'conditions' => $animations_exceptions
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'menu_item_box_shadow_active',
                'selector' => $item . '.ui-e-open > a,' . $item . '.ui-e-current > a',
                'conditions' => $animations_exceptions
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'icon_section_style',
            [
                'label' => esc_html__('Icon', 'uicore-elements'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'icon_position',
            [
                'label' => esc_html__('Position', 'uicore-elements'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'left' => esc_html__('Left', 'uicore-elements'),
                    'right' => esc_html__('Right', 'uicore-elements'),
                ],
                'selectors_dictionary' => [
                    'left' => 'margin-inline-end: var(--ui-e-icon-spacing, 8px); order: 0;',
                    'right' => 'margin-inline-start: var(--ui-e-icon-spacing, 8px); order: 1;',
                ],
                'default' => 'left',
                'selectors' => [
                    $link . ' .ui-e-menu-item-icon' => '{{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => esc_html__('Size', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'size_units' => ['px', 'em', 'rem'],
                'selectors' => [
                    $link . ' > .ui-e-svg-wrap.ui-e-menu-item-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    $link . ' > .ui-e-svg-wrap.ui-e-menu-item-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_spacing',
            [
                'label' => esc_html__('Spacing', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem', 'vw', 'custom'],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 5,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ui-e-icon-spacing: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('icon_style_states');

        $this->start_controls_tab(
            'icon_section_normal',
            [
                'label' => esc_html__('Normal', 'uicore-elements'),
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__('Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    $link . ' > .ui-e-svg-wrap.ui-e-menu-item-icon > *' => 'color: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'icon_section_hover',
            [
                'label' => esc_html__('Hover', 'uicore-elements'),
            ]
        );

        $this->add_control(
            'icon_color_hover',
            [
                'label' => esc_html__('Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    $link . ':hover > .ui-e-svg-wrap.ui-e-menu-item-icon > *' => 'color: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'icon_section_active',
            [
                'label' => esc_html__('Active', 'uicore-elements'),
            ]
        );

        $this->add_control(
            'icon_color_active',
            [
                'label' => esc_html__('Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    $item . '.ui-e-open > a > .ui-e-svg-wrap.ui-e-menu-item-icon > *' => 'color: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'image_section_style',
            [
                'label' => esc_html__('Image', 'uicore-elements'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'image_position',
            [
                'label' => esc_html__('Position', 'uicore-elements'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'left' => esc_html__('Left', 'uicore-elements'),
                    'right' => esc_html__('Right', 'uicore-elements'),
                ],
                'selectors_dictionary' => [
                    'left' => 'margin-inline-end: var(--ui-e-image-spacing, 8px); order: 0;',
                    'right' => 'margin-inline-start: var(--ui-e-image-spacing, 8px); order: 1;',
                ],
                'default' => 'left',
                'selectors' => [
                    $link . ' .ui-e-menu-item-image' => '{{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_size',
            [
                'label' => esc_html__('Size', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'size_units' => ['px', 'em', 'rem'],
                'selectors' => [
                    $link . ' > .ui-e-menu-item-image' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_spacing',
            [
                'label' => esc_html__('Spacing', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem', 'vw', 'custom'],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 5,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ui-e-image-spacing: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_dropdown_indicator_style',
            [
                'label' => esc_html__('Dropdown Indicator', 'uicore-elements'),
                'tab' => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'dropdown_icon[value]',
                            'operator' => '!==',
                            'value' => '',
                        ],
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'dropdown_icon_size',
            [
                'label' => esc_html__('Size', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors' => [
                    $link . ' > .ui-e-dropdown-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                    $link . ' > .ui-e-dropdown-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dropdown_icon_spacing',
            [
                'label' => esc_html__('Spacing', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'default' => [
                    'size' => 5,
                    'unit' => 'px',
                ],
                'selectors' => [
                    $link . ' > .ui-e-dropdown-icon' => 'margin-inline-start: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('dropdown_icon_tabs');

        $this->start_controls_tab('dropdown_icon_tab_normal', [
            'label' => 'Normal',
        ]);
        $this->add_control(
            'dropdown_icon_color_normal',
            [
                'label' => esc_html__('Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    $item . ':not(.ui-e-open) > a > .ui-e-dropdown-icon i' => 'color: {{VALUE}};',
                    $item . ':not(.ui-e-open) > a > .ui-e-dropdown-icon svg' => 'fill: {{VALUE}};',
                ],
                'default' => 'var(--e-global-color-uicore_primary)',

            ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab('dropdown_icon_tab_hover', [
            'label' => 'Hover',
        ]);
        $this->add_control(
            'dropdown_icon_color_hover',
            [
                'label' => esc_html__('Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    $item . ' > a:hover > .ui-e-dropdown-icon i' => 'color: {{VALUE}};',
                    $item . ' > a:hover > .ui-e-dropdown-icon svg' => 'fill: {{VALUE}};',
                ],
                'default' => 'var(--e-global-color-uicore_primary)',

            ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab('dropdown_icon_tab_active', [
            'label' => 'Active',
        ]);
        $this->add_control(
            'dropdown_icon_color_active',
            [
                'label' => esc_html__('Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    $item . '.ui-e-open > a > .ui-e-dropdown-opened i' => 'color: {{VALUE}};',
                    $item . '.ui-e-open > a > .ui-e-dropdown-opened svg' => 'fill: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_badge_style',
            [
                'label' => esc_html__('Badge', 'uicore-elements'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'badge_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'selector' => $link . ' .ui-e-badge',
                'default' => [
                    'font_size' => [
                        'unit' => 'px',
                        'size' => 14,
                    ],
                ],
            ]
        );

        $this->add_control(
            'badge_color',
            [
                'label' => esc_html__('Text Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => '--ui-e-badge-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'badge_background_color',
            [
                'label' => esc_html__('Background Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'default' => 'var(--e-global-color-uicore_primary)',

                'selectors' => [
                    $link . ' .ui-e-badge' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_v_offset',
            [
                'label' => esc_html__('Vertical Offset', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem', '%', 'custom'],
                'range' => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'em' => [
                        'min' => -20,
                        'max' => 20,
                    ],
                    'rem' => [
                        'min' => -20,
                        'max' => 20,
                    ],
                    '%' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ui-e-badge-offset-y: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_h_offset',
            [
                'label' => esc_html__('Horizontal Offset', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem', '%', 'custom'],
                'range' => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'em' => [
                        'min' => -20,
                        'max' => 20,
                    ],
                    'rem' => [
                        'min' => -20,
                        'max' => 20,
                    ],
                    '%' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ui-e-badge-offset-x: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_padding',
            [
                'label' => esc_html__('Padding', 'uicore-elements'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'selectors' => [
                    $link . ' .ui-e-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'badge_border_radius',
            [
                'label' => esc_html__('Border Radius', 'uicore-elements'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'selectors' => [
                    $link . ' .ui-e-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_description_style',
            [
                'label' => esc_html__('Description', 'uicore-elements'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'description_alignment',
            [
                'label' => esc_html__('Alignment', 'uicore-elements'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'uicore-elements'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'uicore-elements'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'uicore-elements'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors_dictionary' => [
                    'left' => 'left: var(--ui-e-description-offset, 0);',
                    'center' => 'left: calc(50% + var(--ui-e-description-offset, 0)); transform: translateX(-50%);',
                    'right' => 'right: var(--ui-e-description-offset, 0);',
                ],
                'selectors' => [
                    $link . ' .ui-e-description' => '{{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => $link . ' .ui-e-description',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => esc_html__('Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    $link . ' .ui-e-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'description_v_offset',
            [
                'label' => esc_html__('Vertical Offset', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem', '%', 'custom'],
                'range' => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'em' => [
                        'min' => -20,
                        'max' => 20,
                    ],
                    'rem' => [
                        'min' => -20,
                        'max' => 20,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => -10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    $link . ' .ui-e-description' => 'bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'description_h_offset',
            [
                'label' => esc_html__('Horizontal Offset', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem', '%', 'custom'],
                'range' => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'em' => [
                        'min' => -20,
                        'max' => 20,
                    ],
                    'rem' => [
                        'min' => -20,
                        'max' => 20,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    $link . ' .ui-e-description' => '--ui-e-description-offset: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Explore the UX of creating/editing menus without style controls to globally adjust nested containers
        // $this->start_controls_section('section_content_style', [
        //     'label' => esc_html__('Sub menu', 'uicore-elements'),
        //     'tab' => Controls_Manager::TAB_STYLE,
        // ]);

        // $this->add_group_control(
        //     Group_Control_Background::get_type(),
        //     [
        //         'name' => 'content_background_color',
        //         'types' => ['classic', 'gradient'],
        //         'exclude' => ['image'],
        //         'selector' => $item . ' > .ui-e-submenu',
        //         'fields_options' => [
        //             'color' => [
        //                 'label' => esc_html__('Background Color', 'uicore-elements'),
        //             ],
        //         ],
        //     ]
        // );

        // $this->add_group_control(
        //     Group_Control_Border::get_type(),
        //     [
        //         'name' => 'content_border',
        //         'selector' => $item . ' > .ui-e-submenu',
        //         'fields_options' => [
        //             'color' => [
        //                 'label' => esc_html__('Border Color', 'uicore-elements'),
        //             ],
        //             'width' => [
        //                 'label' => esc_html__('Border Width', 'uicore-elements'),
        //             ],
        //         ],
        //     ]
        // );

        // $this->add_responsive_control(
        //     'content_border_radius',
        //     [
        //         'label' => esc_html__('Border Radius', 'uicore-elements'),
        //         'type' => Controls_Manager::DIMENSIONS,
        //         'size_units' => ['px', '%', 'em', 'rem', 'custom'],
        //         'selectors' => [
        //             $item . ' > .ui-e-submenu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        //         ],
        //     ]
        // );

        // $this->add_group_control(
        //     Group_Control_Box_Shadow::get_type(),
        //     [
        //         'name' => 'content_shadow',
        //         'selector' => $item . ' > .ui-e-submenu',
        //     ]
        // );

        // $this->add_responsive_control(
        //     'content_padding',
        //     [
        //         'label' => esc_html__('Padding', 'uicore-elements'),
        //         'type' => Controls_Manager::DIMENSIONS,
        //         'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
        //         'selectors' => [
        //             $item . ' > .ui-e-submenu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        //         ],
        //         'separator' => 'before',
        //     ]
        // );

        // $this->end_controls_section();

        $this->start_controls_section('submenu_style_section', [
            'label' => esc_html__('Submenu', 'uicore-elements'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control(
            'submenu_offset',
            [
                'label' => esc_html__('Submenu Offset', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => [
                        'max' => 200,
                    ],
                    'em' => [
                        'max' => 20,
                    ],
                    'rem' => [
                        'max' => 20,
                    ],
                ],
                'default' => [
                    'size' => 0,
                ],
                'frontend_available' => true,
                'selectors' => [
                    '{{WRAPPER}}' => '--ui-e-submenu-offset: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section('back_button_section', [
            'label' => esc_html__('Back Button', 'uicore-elements'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'fullscreen_canvas_dropdown' => 'slide',
                'breakpoint_selector!' => 'none'
            ],
        ]);

        $this->start_controls_tabs('back_button_tabs');

        $this->start_controls_tab('back_default_tab', [
            'label' => esc_html__('Default', 'uicore-elements'),
        ]);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'back_button_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'selector' => '{{WRAPPER}} .ui-e-menu-item > .ui-e-back-button',
            ]
        );

        $this->add_control(
            'back_button_color',
            [
                'label' => esc_html__('Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ui-e-menu-item > .ui-e-back-button' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ui-e-menu-item > .ui-e-back-button .ui-e-back-button-icon' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'fullscreen_canvas_dropdown' => 'slide',
                ],
            ]
        );

        $this->add_responsive_control(
            'back_button_icon_size',
            [
                'label' => esc_html__('Size', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ui-e-menu-item > .ui-e-back-button .ui-e-back-button-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ui-e-menu-item > .ui-e-back-button .ui-e-back-button-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'back_button_offset_x',
            [
                'label' => esc_html__('Horizontal Offset', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'default' => [
                    'unit' => '%',
                    'size' => 0,
                ],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 2,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ui-e-back-button-offset-x: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'fullscreen_canvas_dropdown' => 'slide',
                ],
            ]
        );

        $this->add_control(
            'back_button_offset_y',
            [
                'label' => esc_html__('Vertical Offset', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'default' => [
                    'unit' => '%',
                    'size' => -8,
                ],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 2,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ui-e-back-button-offset-y: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'fullscreen_canvas_dropdown' => 'slide',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab('back_submenu_tab', [
            'label' => esc_html__('Submenu', 'uicore-elements'),
        ]);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'back_button_typography_submenu',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'selector' => '{{WRAPPER}} .ui-e-menu-list > .ui-e-back-button',
            ]
        );

        $this->add_control(
            'back_button_color_submenu',
            [
                'label' => esc_html__('Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ui-e-menu-list > .ui-e-back-button' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ui-e-menu-list > .ui-e-back-button .ui-e-back-button-icon' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'fullscreen_canvas_dropdown' => 'slide',
                ],
            ]
        );

        $this->add_responsive_control(
            'back_button_icon_size_submenu',
            [
                'label' => esc_html__('Size', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ui-e-menu-list > .ui-e-back-button .ui-e-back-button-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ui-e-menu-list > .ui-e-back-button .ui-e-back-button-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section('fullscreen_menu_button_section', [
            'label' => esc_html__('Fullscreen Button', 'uicore-elements'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'breakpoint_selector!' => 'none'
            ]
        ]);

        $this->add_control(
            'fullscreen_button_size',
            [
                'label' => esc_html__('Button Size', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'range' => [
                    'px' => [
                        'min' => 14,
                        'max' => 80,
                        'step' => 1,
                    ],

                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ui-e-fullscreen-button-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'button_style!' => 'text',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'fullscreen_button_typography',
                'fields_options' => [
                    'font_size' => [
                        'default' => [
                            'unit' => 'px',
                            'size' => 12,
                        ],
                    ],
                    'line_height' => [
                        'default' => [
                            'unit' => 'em',
                            'size' => 1,
                        ],
                    ],
                ],
                'selector' => '{{WRAPPER}} .ui-e-fullscreen-button-text',
                'condition' => [
                    'button_style' => 'text',
                ],
            ]
        );

        $this->start_controls_tabs('fullscreen_menu_button_tabs');

        // Open Button
        $this->start_controls_tab('fullscreen_menu_button_tab_normal', [
            'label' => '<span class="elementor-control-icons--inline__displayed-icon"><i class="fas fa-bars"></i></span>'
        ]);


        $this->add_control(
            'fullscreen_button_padding',
            [
                'label' => esc_html__('Button Padding', 'uicore-elements'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'default' => [
                    'unit' => 'px',
                    'top' => 6,
                    'right' => 6,
                    'bottom' => 6,
                    'left' => 6,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ui-e-fullscreen-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'fullscreen_button_radius',
            [
                'label' => esc_html__('Border Radius', 'uicore-elements'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'default' => [
                    'unit' => 'px',
                    'top' => 6,
                    'right' => 6,
                    'bottom' => 6,
                    'left' => 6,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ui-e-fullscreen-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'selector' => '{{WRAPPER}} .ui-e-fullscreen-button',
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'fullscreen_button_background',
            [
                'label' => esc_html__('Background Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ui-e-fullscreen-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'fullscreen_button_color',
            [
                'label' => esc_html__('Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ui-e-fullscreen-button' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ui-e-fullscreen-button svg' => 'fill: {{VALUE}};',
                ],
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'fullscreen_button_background_hover',
            [
                'label' => esc_html__('Hover Background Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ui-e-fullscreen-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'fullscreen_button_color_hover',
            [
                'label' => esc_html__('Hover Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ui-e-fullscreen-button:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ui-e-fullscreen-button:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // Close Button
        $this->start_controls_tab('fullscreen_button_tab_active', [
            'label' => '<span class="elementor-control-icons--inline__displayed-icon"><i class="fas fa-window-close"></i></span>'
        ]);

        $this->add_control(
            'fullscreen_close_button_description',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => esc_html__('The close button position shown in the editor may differ from the frontend. Check the frontend to confirm its final placement.', 'uicore-elements'),
                'content_classes' => 'elementor-control-field-description',
            ]
        );

        $this->add_responsive_control(
            'fullscreen_close_button_offset_x',
            [
                'label' => esc_html__('Horizontal Offset', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['vw'],
                'default' => [
                    'unit' => 'vw',
                    'size' => 0,
                ],
                'range' => [
                    'vw' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}.ui-e-fullscreen-menu-enabled .ui-e-fullscreen-button-wrp' => '--ui-e-fullscreen-button-offset-x: {{SIZE}}vw;',
                ],
            ]
        );

        $this->add_responsive_control(
            'fullscreen_close_button_offset_y',
            [
                'label' => esc_html__('Vertical Offset', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['vh'],
                'default' => [
                    'unit' => 'vh',
                    'size' => 0,
                ],
                'range' => [
                    'vh' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}}.ui-e-fullscreen-menu-enabled .ui-e-fullscreen-button-wrp' => '--ui-e-fullscreen-button-offset-y: {{SIZE}}vh;',
                ],
            ]
        );

        $this->add_control(
            'fullscreen_button_padding_active',
            [
                'label' => esc_html__('Padding', 'uicore-elements'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'selectors' => [
                    '{{WRAPPER}} .ui-e-fullscreen-button[aria-expanded="true"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'fullscreen_button_radius_active',
            [
                'label' => esc_html__('Border Radius', 'uicore-elements'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'selectors' => [
                    '{{WRAPPER}} .ui-e-fullscreen-button[aria-expanded="true"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'fullscreen_button_border_active',
                'selector' => '{{WRAPPER}} .ui-e-fullscreen-button[aria-expanded="true"]',
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'fullscreen_button_background_active',
            [
                'label' => esc_html__('Background Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ui-e-fullscreen-button[aria-expanded="true"]' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'fullscreen_button_color_active',
            [
                'label' => esc_html__('Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ui-e-fullscreen-button[aria-expanded="true"]' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ui-e-fullscreen-button[aria-expanded="true"] svg' => 'fill: {{VALUE}};',
                ],
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'fullscreen_button_background_hover_active',
            [
                'label' => esc_html__('Hover Background Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ui-e-fullscreen-button[aria-expanded="true"]:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'fullscreen_button_color_hover_active',
            [
                'label' => esc_html__('Hover Color', 'uicore-elements'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ui-e-fullscreen-button[aria-expanded="true"]:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ui-e-fullscreen-button[aria-expanded="true"]:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section('fullscreen_menu_canvas_section', [
            'label' => esc_html__('Fullscreen Canvas', 'uicore-elements'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'breakpoint_selector!' => 'none'
            ]
        ]);

        $this->add_control(
            'canvas_heading',
            [
                'label' => esc_html__('Canvas', 'uicore-elements'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'fullscreen_canvas_background',
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => $fullscreen_canvas,
                'fields_options' => [
                    'color' => [
                        'label' => esc_html__('Canvas Background', 'uicore-elements'),
                    ],
                ],
            ]
        );

        // Add blur slider control
        $this->add_responsive_control(
            'fullscreen_canvas_blur',
            [
                'label' => esc_html__('Background Blur', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0.1,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}}.ui-e-fullscreen-menu-enabled > nav' => 'backdrop-filter: blur({{SIZE}}{{UNIT}}); -webkit-backdrop-filter: blur({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->add_control(
            'canvas_inner_container_heading',
            [
                'label' => esc_html__('Inner container', 'uicore-elements'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'fullscreen_canvas_container_background',
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => $fullscreen_canvas . ' > .ui-e-menu',
                'fields_options' => [
                    'color' => [
                        'label' => esc_html__('Container Background', 'uicore-elements'),
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'fullscreen_canvas_padding',
            [
                'label' => esc_html__('Padding', 'uicore-elements'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'default' => [
                    'unit' => 'px',
                    'top' => 60,
                    'right' => 60,
                    'bottom' => 60,
                    'left' => 60,
                ],
                'tablet_default' => [
                    'unit' => 'px',
                    'top' => 50,
                    'right' => 50,
                    'bottom' => 50,
                    'left' => 50,
                ],
                'mobile_default' => [
                    'unit' => 'px',
                    'top' => 40,
                    'right' => 40,
                    'bottom' => 40,
                    'left' => 40,
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ui-e-fullscreen-canvas-padding-l: {{LEFT}}{{UNIT}}; --ui-e-fullscreen-canvas-padding-r: {{RIGHT}}{{UNIT}};',
                    $fullscreen_canvas . ' > .ui-e-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Add border group control for the inner container
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'fullscreen_canvas_container_border',
                'selector' => $fullscreen_canvas . ' > .ui-e-menu',
                'fields_options' => [
                    'color' => [
                        'label' => esc_html__('Border Color', 'uicore-elements'),
                    ],
                    'width' => [
                        'label' => esc_html__('Border Width', 'uicore-elements'),
                    ],
                ],
            ]
        );

        $this->add_control(
            'fullscreen_canvas_radius',
            [
                'label' => esc_html__('Border Radius', 'uicore-elements'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'selectors' => [
                    $fullscreen_canvas . ' > .ui-e-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'fullscreen_canvas_width',
            [
                'label' => esc_html__('Width', 'uicore-elements'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['vw'],
                'range' => [
                    'vw' => [
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 100,
                    'unit' => 'vw',
                ],
                'tablet_default' => [
                    'size' => 100,
                    'unit' => 'vw',
                ],
                'mobile_default' => [
                    'size' => 100,
                    'unit' => 'vw',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--ui-e-fullscreen-canvas-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function get_current_request_signature()
    {
        global $wp;

        $request_path = '';

        if (!empty($wp) && isset($wp->request)) {
            $request_path = $wp->request;
        }

        return $this->normalize_menu_item_url(home_url('/' . ltrim($request_path, '/')));
    }

    protected function normalize_menu_item_url($url)
    {
        if (!is_string($url)) {
            return null;
        }

        $url = trim($url);

        if ('' === $url) {
            return null;
        }

        $url_scheme = strtolower((string) wp_parse_url($url, PHP_URL_SCHEME));

        if (
            '#' === $url[0]
            || in_array($url_scheme, ['javascript', 'mailto', 'tel'], true)
        ) {
            return null;
        }

        if (0 === strpos($url, '//')) {
            $url = (is_ssl() ? 'https:' : 'http:') . $url;
        }

        $parsed_url = wp_parse_url($url);

        if (false === $parsed_url) {
            return null;
        }

        if (empty($parsed_url['host'])) {
            $relative_path = $parsed_url['path'] ?? '';
            $url = home_url('/' . ltrim($relative_path, '/'));
            $parsed_url = wp_parse_url($url);

            if (false === $parsed_url) {
                return null;
            }
        }

        $host = strtolower((string) ($parsed_url['host'] ?? ''));

        if ('' === $host) {
            return null;
        }

        $port = isset($parsed_url['port']) ? ':' . (int) $parsed_url['port'] : '';
        $path = '/' . ltrim((string) ($parsed_url['path'] ?? '/'), '/');
        $path = untrailingslashit($path);

        if ('' === $path) {
            $path = '/';
        }

        return $host . $port . $path;
    }

    protected function is_current_menu_item($url)
    {
        $item_signature = $this->normalize_menu_item_url($url);

        if (null === $item_signature) {
            return false;
        }

        $current_signature = $this->get_current_request_signature();

        if (null === $current_signature) {
            return false;
        }

        return $item_signature === $current_signature;
    }

    // TODO: study the possibility of improving the is_option() method so we can use it here.
    // The issue relies on how is_option just take all settings as base, and in here we're
    // working mainly with a selected collection of settings from the repeaters.
    protected function render_menu_item($settings)
    {
        // Data
        $index      = $settings['index'];

        $custom_id  = $settings['item']['element_id'];
        $image      = $settings['item']['item_image'];
        $icon       = $settings['item']['item_icon'];
        $badge      = $settings['item']['item_badge'];
        $description = $settings['item']['item_description'];
        $dropdown   = $this->get_settings_for_display('dropdown_icon');
        $dropdown_active = $this->get_settings_for_display('dropdown_icon_active');

        $fullscreen_dropdown = $this->get_settings_for_display('dropdown_fullscreen_icon');
        $fullscreen_dropdown_active = $this->get_settings_for_display('dropdown_fullscreen_icon_active');

        $highlight_current = $this->is_option('highlight_current', 'yes');
        $has_submenu = $settings['item']['item_submenu'] && $settings['item']['item_submenu'] === 'yes';
        $item_url = $settings['item']['item_link']['url'] ?? '#';
        $url = empty($item_url) ? ['url' => '#'] : $settings['item']['item_link'];

        // Url atts
        $this->add_link_attributes('link_' . $index, $url);

        // Item atts
        $this->add_render_attribute('item_' . $index, ['class' => 'ui-e-menu-item']);
        $this->add_render_attribute('item_' . $index, ['data-menu-content-id' => $settings['container_id']]);
        if ($has_submenu) {
            $width = 'ui-e-' . $settings['item']['item_submenu_width'];

            $this->add_render_attribute('item_' . $index, ['class' => 'ui-e-has-submenu']);
            $this->add_render_attribute('item_' . $index, ['class' => esc_attr($width)]);
        }
        if ($custom_id) {
            $this->add_render_attribute('item_' . $index, ['id' => esc_attr($custom_id)]);
        }
        if ($highlight_current && $this->is_current_menu_item($item_url)) {
            $this->add_render_attribute('item_' . $index, ['class' => 'ui-e-current']);
        }

        ob_start();
?>
        <li <?php $this->print_render_attribute_string('item_' . $index); ?>>

            <a <?php $this->print_render_attribute_string('link_' . $index); ?>>

                <?php
                if (!empty($image['url'])) {
                    echo wp_kses_post(wp_get_attachment_image(
                        $image['id'],
                        'thumbnail',
                        false,
                        [
                            'alt' => esc_attr($image['alt']),
                            'class' => 'ui-e-menu-item-image',
                        ]
                    ));
                }
                ?>

                <?php if (!empty($icon['value'])) : ?>
                    <span class="ui-e-svg-wrap ui-e-menu-item-icon">
                        <?php Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']); ?>
                    </span>
                <?php endif; ?>

                <span class="ui-e-text">
                    <?php echo esc_html($settings['item']['item_title']); ?>

                    <?php if (!empty($description)) : ?>
                        <span class="ui-e-description"> <?php echo esc_html($description); ?> </span>
                    <?php endif; ?>

                    <?php if (!empty($badge)) : ?>
                        <span class="ui-e-badge"> <?php echo esc_html($badge); ?> </span>
                    <?php endif; ?>
                </span>

                <?php if ($has_submenu && !empty($dropdown['value'])) : ?>
                    <span class="ui-e-svg-wrap ui-e-dropdown-icon">

                        <?php Icons_Manager::render_icon($dropdown, ['aria-hidden' => 'true', 'class' => 'ui-e-dropdown-closed']); ?>

                        <?php if (!empty($dropdown_active['value'])) : ?>
                            <?php Icons_Manager::render_icon($dropdown_active, ['aria-hidden' => 'true', 'class' => 'ui-e-dropdown-opened']); ?>
                        <?php endif; ?>

                    </span>
                <?php endif; ?>

                <?php if ($has_submenu && !empty($fullscreen_dropdown['value'])) : ?>
                    <span class="ui-e-svg-wrap ui-e-dropdown-icon" data-fullscreen-layout>

                        <?php Icons_Manager::render_icon($fullscreen_dropdown, ['aria-hidden' => 'true', 'class' => 'ui-e-dropdown-closed']); ?>

                        <?php if (!empty($fullscreen_dropdown_active['value'])) : ?>
                            <?php Icons_Manager::render_icon($fullscreen_dropdown_active, ['aria-hidden' => 'true', 'class' => 'ui-e-dropdown-opened']); ?>
                        <?php endif; ?>

                    </span>
                <?php endif; ?>

            </a>

            <?php
            if ($has_submenu) {
                echo $this->render_sub_menu($settings); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
            ?>

        </li>
    <?php
        return ob_get_clean();
    }

    protected function render_sub_menu($settings)
    {
        ob_start();

        $this->print_child($settings['index'], $settings);


        return ob_get_clean();
    }

    protected function is_fullscreen_menu_widget($item)
    {
        return !empty($item['item_convert_to_widget']) && 'yes' === $item['item_convert_to_widget'];
    }

    protected function render_fullscreen_menu_widget($settings)
    {
        ob_start();

        $this->print_child($settings['index'], [
            'container_id' => $settings['container_id'],
            'item_count' => $settings['item_count'],
            'is_fullscreen_menu_widget' => true,
        ]);

        return ob_get_clean();
    }

    protected function render_fullscreen_menu_button()
    {
        $button_style = $this->get_settings_for_display('button_style');
        $fullscreen_canvas_id = 'ui-e-fullscreen-menu-' . $this->get_id_int();

        ob_start();
    ?>
        <div class="ui-e-fullscreen-button-wrp">
            <button class="ui-e-fullscreen-button" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr($fullscreen_canvas_id); ?>">
                <span class="screen-reader-text"><?php echo esc_html__('Open menu button', 'uicore-elements'); ?></span>

                <?php if ($button_style === 'text') : ?>

                    <span class="ui-e-fullscreen-button-text ui-e-open"> <?php echo esc_html($this->get_settings_for_display('button_text_open')); ?> </span>
                    <span class="ui-e-fullscreen-button-text ui-e-close"> <?php echo esc_html($this->get_settings_for_display('button_text_close')); ?> </span>

                <?php elseif ($button_style !== 'custom') : ?>

                    <span class="ui-e-bars">
                        <span class="ui-e-bar"></span>
                        <span class="ui-e-bar"></span>
                        <span class="ui-e-bar"></span>
                    </span>

                <?php else : ?>

                    <?php
                    $open_icon = $this->get_settings_for_display('fullscreen_menu_open_icon');
                    $close_icon = $this->get_settings_for_display('fullscreen_menu_close_icon');

                    if (!empty($open_icon['value'])) : ?>
                        <span class="ui-e-fullscreen-button-icon ui-e-fullscreen-button-open">
                            <?php Icons_Manager::render_icon($open_icon, ['aria-hidden' => 'true']); ?>
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($close_icon['value'])) : ?>
                        <span class="ui-e-fullscreen-button-icon ui-e-fullscreen-button-close">
                            <?php Icons_Manager::render_icon($close_icon, ['aria-hidden' => 'true']); ?>
                        </span>
                    <?php endif; ?>

                <?php endif; ?>
            </button>
        </div>
    <?php

        return ob_get_clean();
    }

    protected function get_back_button_icon_markup($icon = null)
    {
        if (empty($icon)) {
            $icon = $this->get_settings_for_display('back_button_icon');
        }

        if (empty($icon['value'])) {
            return '';
        }

        return Icons_Manager::try_get_icon_html(
            $icon,
            [
                'aria-hidden' => 'true',
                'class' => 'ui-e-back-button-icon-markup',
            ]
        );
    }

    protected function render_back_button_icon_template($icon = null)
    {
        $icon_markup = $this->get_back_button_icon_markup($icon);

        if ('' === $icon_markup) {
            return '';
        }

        ob_start();
    ?>
        <span class="ui-e-back-button-icon-template" hidden aria-hidden="true">
            <?php echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>
        </span>
    <?php

        return ob_get_clean();
    }

    protected function get_fullscreen_breakpoint_config()
    {
        $breakpoint_key = (string) $this->get_settings_for_display('breakpoint_selector');

        if ('' === $breakpoint_key || 'none' === $breakpoint_key) {
            return [
                'key' => 'none',
                'value' => null,
            ];
        }

        if ('desktop' === $breakpoint_key) {
            return [
                'key' => 'desktop',
                'value' => null,
            ];
        }

        $breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
        $breakpoint_value = isset($breakpoints[$breakpoint_key])
            ? (int) $breakpoints[$breakpoint_key]->get_value()
            : null;

        return [
            'key' => $breakpoint_key,
            'value' => $breakpoint_value,
        ];
    }

    /**
     * Print the content area.
     *
     * @param int $index
     * @param array $settings
     */
    public function print_child($index, $settings = [])
    {
        $children = $this->get_children();
        $child_ids = [];
        if (empty($children)) {
            return;
        }
        foreach ($children as $child) {
            $child_ids[] = $child->get_id();
        }

        $add_attribute_to_container = function ($should_render, $container) use ($settings, $child_ids) {
            if (in_array($container->get_id(), $child_ids)) {
                $this->add_attributes_to_container($container, $settings);
            }

            return $should_render;
        };

        if (empty($children[$index])) {
            return;
        }

        add_filter('elementor/frontend/container/should_render', $add_attribute_to_container, 10, 3);
        $children[$index]->print_element();
        remove_filter('elementor/frontend/container/should_render', $add_attribute_to_container);
    }

    protected function add_attributes_to_container($container, $settings)
    {
        if (!empty($settings['is_fullscreen_menu_widget'])) {
            $container->add_render_attribute('_wrapper', [
                'id' => $settings['container_id'],
                'data-menu-index' => $settings['item_count'],
                'class' => ['ui-e-widget-area'],
            ]);

            return;
        }

        $container->add_render_attribute('_wrapper', [
            'id' => $settings['container_id'],
            'data-menu-index' => $settings['item_count'],
            'class' => ['ui-e-menu-content', 'ui-e-submenu'],
        ]);
    }

    /**
     * Injects basic styles that prevents the fullscreen CSS from mobile to experiencing big layout shifts, since
     * the classname that triggers the fullscreen styles is added after DOMContentLoaded by the main widget JS, and can cause
     */
    protected function critical_fullscreen_css()
    {
        $breakpoint_config = $this->get_fullscreen_breakpoint_config();

        if (in_array($breakpoint_config['key'], ['none', 'desktop'], true)) {
            return;
        }

        $id = $this->get_id();

        echo '
            <style data-ui-e-fullscreen-critical-css>
                @media screen and (max-width: ' . (int) $breakpoint_config['value'] . 'px) {
                    .elementor-element-' . $id . ':not(.ui-e-inside-fullscreen) > .ui-e-advanced-menu-nav {
                        visibility: hidden;
                        position: fixed;
                    }
                }
            </style>
        ';
    }

    protected function render()
    {
        if (Plugin::$instance->experiments->is_feature_active('nested-elements') == false) {
            $this->nesting_fallback();
            return;
        }

        if (Plugin::$instance->experiments->is_feature_active('e_optimized_markup') == false) {
            return;
        }

        $this->critical_fullscreen_css();

        $settings = $this->get_settings_for_display();
        $menu_titles = '';
        $fullscreen_widgets = '';
        $widget_number = $this->get_id_int();
        $fullscreen_canvas_id = 'ui-e-fullscreen-menu-' . $widget_number;

        $this->add_render_attribute('_wrapper', 'class', 'ui-e-is-loading');

        $this->add_render_attribute(
            'nav',
            [
                'class' => 'ui-e-advanced-menu-nav',
                'data-widget-number' => $this->get_widget_number(),
                'id' => $fullscreen_canvas_id,
            ]
        );

        if ($settings['menu_name']) {
            $this->add_render_attribute('nav', 'aria-label', $settings['menu_name']);
        }

        if ($this->is_option('widget_area_only', 'yes')) {
            $this->add_render_attribute('wrapper', 'class', ['ui-e-menu', 'ui-e-widget-only']);
        } else {
            $this->add_render_attribute('wrapper', 'class', 'ui-e-menu');
        }

        foreach ($settings['menu_items'] as $index => $item) {
            $item_count = $index + 1;

            $item_settings = [
                'index' => $index,
                'item_count' => $item_count,
                'container_id' => 'ui-e-menu-content-' . $widget_number . $item_count,
                'item' => $item,
            ];

            if ($this->is_fullscreen_menu_widget($item)) {
                $fullscreen_widgets .= $this->render_fullscreen_menu_widget($item_settings);
                continue;
            }

            $menu_titles .= $this->render_menu_item($item_settings);
        }

        echo $this->render_fullscreen_menu_button(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

    ?>
        <nav <?php $this->print_render_attribute_string('nav'); ?>>
            <?php echo $this->render_back_button_icon_template(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>

            <div <?php $this->print_render_attribute_string('wrapper'); ?>>
                <ul class="ui-e-menu-list">
                    <?php echo $menu_titles; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    ?>
                </ul>
                <?php echo $fullscreen_widgets; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                ?>
            </div>
        </nav>
        <?php
    }
    protected function content_template()
    {
        if (Plugin::$instance->experiments->is_feature_active('nested-elements') == false) {
            return;
        }

        if (!Plugin::$instance->experiments->is_feature_active('e_optimized_markup')) {
        ?>
            <div class="ui-e-optimized-markup-alert">
                <?php echo esc_html__('This widget requires the Optimized Markup experiment to be enabled in Elementor settings', 'uicore-elements'); ?>
            </div>
        <?php
            return;
        }
        ?>
        <# const elementUid=view.getIDInt().toString(); #>
            <#
                const menuItems=settings.menu_items || [],
                wrapperKey='wrapper-' + elementUid,
                buttonStyle=settings.button_style || 'default' ,
                fullscreenCanvasId='ui-e-fullscreen-menu-' + elementUid,
                fullscreenBtnOpenText=settings.button_text_open || '' ,
                fullscreenBtnCloseText=settings.button_text_close || '' ,
                backButtonIcon=settings.back_button_icon && settings.back_button_icon.value
                ? elementor.helpers.renderIcon( view, settings.back_button_icon, { 'aria-hidden' : true, 'class' : 'ui-e-back-button-icon-markup' }, 'i' , 'object' )
                : false,
                fullscreenBtnOpenIcon=settings.fullscreen_menu_open_icon && settings.fullscreen_menu_open_icon.value
                ? elementor.helpers.renderIcon( view, settings.fullscreen_menu_open_icon, { 'aria-hidden' : true }, 'i' , 'object' )
                : false,
                fullscreenBtnCloseIcon=settings.fullscreen_menu_close_icon && settings.fullscreen_menu_close_icon.value
                ? elementor.helpers.renderIcon( view, settings.fullscreen_menu_close_icon, { 'aria-hidden' : true }, 'i' , 'object' )
                : false;

                if ( settings.widget_area_only==='yes' ) {
                view.addRenderAttribute( wrapperKey, { 'class' : [ 'ui-e-menu' , 'ui-e-widget-only' ] } );
                } else {
                view.addRenderAttribute( wrapperKey, { 'class' : [ 'ui-e-menu' ] } );
                }

                #>
                <div class="ui-e-fullscreen-button-wrp">
                    <button class="ui-e-fullscreen-button" type="button" aria-expanded="false" aria-controls="{{ fullscreenCanvasId }}">
                        <span class="screen-reader-text"><?php echo esc_html__('Open menu button', 'uicore-elements'); ?></span>

                        <# if ( buttonStyle==='text' ) { #>
                            <span class="ui-e-fullscreen-button-text ui-e-open">{{{ fullscreenBtnOpenText }}}</span>
                            <span class="ui-e-fullscreen-button-text ui-e-close">{{{ fullscreenBtnCloseText }}}</span>
                            <# } else if ( buttonStyle !=='custom' ) { #>
                                <span class="ui-e-bars">
                                    <span class="ui-e-bar"></span>
                                    <span class="ui-e-bar"></span>
                                    <span class="ui-e-bar"></span>
                                </span>
                                <# } else { #>
                                    <# if ( fullscreenBtnOpenIcon && fullscreenBtnOpenIcon.value ) { #>
                                        <span class="ui-e-fullscreen-button-icon ui-e-fullscreen-button-open">{{{ fullscreenBtnOpenIcon.value }}}</span>
                                        <# } #>

                                            <# if ( fullscreenBtnCloseIcon && fullscreenBtnCloseIcon.value ) { #>
                                                <span class="ui-e-fullscreen-button-icon ui-e-fullscreen-button-close">{{{ fullscreenBtnCloseIcon.value }}}</span>
                                                <# } #>
                                                    <# } #>
                    </button>
                </div>
                <nav class="ui-e-advanced-menu-nav" id="{{ fullscreenCanvasId }}" data-widget-number="{{ elementUid }}"
                    <# if ( settings.menu_name ) { #>aria-label="{{ settings.menu_name }}"<# } #>>
                        <# if ( backButtonIcon && backButtonIcon.value ) { #>
                            <span class="ui-e-back-button-icon-template" hidden aria-hidden="true">{{{ backButtonIcon.value }}}</span>
                            <# } #>
                                <div {{{ view.getRenderAttributeString( wrapperKey ) }}}>
                                    <ul class="ui-e-menu-list">
                                        <# if ( menuItems.length ) {
                                            _.each( menuItems, function( item, index ) {
                                            const itemCount=index + 1,
                                            itemUid=elementUid + itemCount,
                                            itemKey='item-' + itemUid,
                                            linkKey='link-' + itemUid,
                                            imageKey='image-' + itemUid,
                                            textKey='text-' + itemUid,
                                            titleKey='title-' + itemUid,
                                            descriptionKey='description-' + itemUid,
                                            badgeKey='badge-' + itemUid,
                                            iconKey='icon-' + itemUid,
                                            dropdownKey='dropdown-' + itemUid,
                                            itemIcon=item.item_icon && item.item_icon.value
                                            ? elementor.helpers.renderIcon( view, item.item_icon, { 'aria-hidden' : true, 'class' : 'ui-e-menu-item-icon' }, 'i' , 'object' )
                                            : false,
                                            dropdownIcon=settings.dropdown_icon && settings.dropdown_icon.value
                                            ? elementor.helpers.renderIcon( view, settings.dropdown_icon, { 'aria-hidden' : true, 'class' : 'ui-e-dropdown-closed' }, 'i' , 'object' )
                                            : false,
                                            dropdownIconActive=settings.dropdown_icon_active && settings.dropdown_icon_active.value
                                            ? elementor.helpers.renderIcon( view, settings.dropdown_icon_active, { 'aria-hidden' : true, 'class' : 'ui-e-dropdown-opened' }, 'i' , 'object' )
                                            : false,
                                            fullscreenDropdownIcon=settings.dropdown_fullscreen_icon && settings.dropdown_fullscreen_icon.value
                                            ? elementor.helpers.renderIcon( view, settings.dropdown_fullscreen_icon, { 'aria-hidden' : true, 'class' : 'ui-e-dropdown-closed' }, 'i' , 'object' )
                                            : false,
                                            fullscreenDropdownIconActive=settings.dropdown_fullscreen_icon_active && settings.dropdown_fullscreen_icon_active.value
                                            ? elementor.helpers.renderIcon( view, settings.dropdown_fullscreen_icon_active, { 'aria-hidden' : true, 'class' : 'ui-e-dropdown-opened' }, 'i' , 'object' )
                                            : false,
                                            imageData=item.item_image && item.item_image.url
                                            ? {
                                            id: item.item_image.id,
                                            url: item.item_image.url,
                                            size: 'thumbnail' ,
                                            model: view.getEditModel()
                                            }
                                            : false,
                                            imageUrl=imageData ? elementor.imagesManager.getImageUrl( imageData ) : '' ,
                                            itemConvertToWidget=item.item_convert_to_widget && item.item_convert_to_widget==='yes' ,
                                            hasSubmenu=item.item_submenu && item.item_submenu==='yes' ,
                                            submenuWidthClass='ui-e-' + ( item.item_submenu_width || 'widget-width' ) ,
                                            submenuId='ui-e-menu-content-' + itemUid,
                                            submenuKey='submenu-' + itemUid;

                                            const itemClasses=[ 'ui-e-menu-item' ];

                                            if ( itemConvertToWidget ) {
                                            itemClasses.push( 'ui-e-converted-widget-placeholder' );
                                            }

                                            if ( hasSubmenu ) {
                                            itemClasses.push( 'ui-e-has-submenu' );
                                            itemClasses.push( submenuWidthClass );
                                            }

                                            view.addRenderAttribute( itemKey, { 'class' : itemClasses } );
                                            view.addRenderAttribute( itemKey, 'data-menu-content-id' , submenuId );
                                            view.addRenderAttribute( itemKey, 'data-menu-index' , itemCount );

                                            if ( item.element_id ) {
                                            view.addRenderAttribute( itemKey, 'id' , item.element_id );
                                            }

                                            const linkAttrs={};

                                            if ( item.item_link && item.item_link.url ) {
                                            linkAttrs.href=item.item_link.url;
                                            }

                                            if ( item.item_link && item.item_link.is_external ) {
                                            linkAttrs.target='_blank' ;
                                            linkAttrs.rel='noopener noreferrer' ;
                                            }

                                            if ( item.item_link && item.item_link.nofollow ) {
                                            linkAttrs.rel=( linkAttrs.rel ? linkAttrs.rel + ' ' : '' ) + 'nofollow' ;
                                            }

                                            view.addRenderAttribute( linkKey, linkAttrs );

                                            view.addRenderAttribute( titleKey, { 'data-binding-type' : 'repeater-item' , 'data-binding-repeater-name' : 'menu_items' , 'data-binding-setting' : [ 'item_title' , 'element_id' ], 'data-binding-index' : itemCount, 'data-binding-config' : JSON.stringify({ 'element_id' : { editType : 'attribute' , attr : 'id' , selector : 'li' }, 'item_title' : { editType : 'text' } }),
                                            } );

                                            view.addRenderAttribute( badgeKey, { 'class' : [ 'ui-e-badge' ], 'data-binding-type' : 'repeater-item' , 'data-binding-repeater-name' : 'menu_items' , 'data-binding-setting' : [ 'item_badge' ], 'data-binding-index' : itemCount, 'data-binding-config' : JSON.stringify({ 'item_badge' : { editType : 'text' } }),
                                            } );

                                            view.addRenderAttribute( imageKey, { 'class' : [ 'ui-e-menu-item-image' ] } );

                                            view.addRenderAttribute( textKey, { 'class' : [ 'ui-e-text' ] } );

                                            view.addRenderAttribute( iconKey, { 'class' : [ 'ui-e-svg-wrap' , 'ui-e-menu-item-icon' ], 'data-binding-type' : 'repeater-item' , 'data-binding-repeater-name' : 'menu_items' , 'data-binding-setting' : [ 'item_icon.value' ], 'data-binding-index' : itemCount, 'data-binding-config' : JSON.stringify({ 'item_icon.value' : { editType : 'text' } }),
                                            } );

                                            view.addRenderAttribute( descriptionKey, { 'class' : [ 'ui-e-description' ], 'data-binding-type' : 'repeater-item' , 'data-binding-repeater-name' : 'menu_items' , 'data-binding-setting' : [ 'item_description' ], 'data-binding-index' : itemCount, 'data-binding-config' : JSON.stringify({ 'item_description' : { editType : 'text' } }),
                                            } );

                                            view.addRenderAttribute( dropdownKey, { 'class' : [ 'ui-e-svg-wrap' , 'ui-e-dropdown-icon' ] } );
                                            view.addRenderAttribute( dropdownKey + '-fullscreen' , { 'class' : [ 'ui-e-svg-wrap' , 'ui-e-dropdown-icon' ], 'data-fullscreen-layout' : true } );

                                            view.addRenderAttribute( submenuKey, { 'class' : [ 'ui-e-submenu-placeholder' ], 'data-container-id' : submenuId, 'data-menu-index' : itemCount, 'hidden' : hasSubmenu ? false : 'hidden'
                                            } );

                                            #>
                                            <# if ( itemConvertToWidget ) { #>
                                                <li {{{ view.getRenderAttributeString( itemKey ) }}} style="display:none;"></li>
                                                <# } else { #>
                                                    <li {{{ view.getRenderAttributeString( itemKey ) }}}>
                                                        <a {{{ view.getRenderAttributeString( linkKey ) }}}>
                                                            <# if ( imageUrl ) { #>
                                                                <img {{{ view.getRenderAttributeString( imageKey ) }}} src="{{{ imageUrl }}}" alt="{{{ item.item_image.alt || '' }}}" />
                                                                <# } #>

                                                                    <# if ( itemIcon && itemIcon.value ) { #>
                                                                        <span {{{ view.getRenderAttributeString( iconKey ) }}}>
                                                                            {{{ itemIcon.value }}}
                                                                        </span>
                                                                        <# } #>

                                                                            <span {{{ view.getRenderAttributeString( textKey ) }}}>
                                                                                <span {{{ view.getRenderAttributeString( titleKey ) }}}> {{{ item.item_title }}} </span>

                                                                                <# if ( item.item_description ) { #>
                                                                                    <span {{{ view.getRenderAttributeString( descriptionKey ) }}}>{{{ item.item_description }}}</span>
                                                                                    <# } #>

                                                                                        <# if ( item.item_badge ) { #>
                                                                                            <span {{{ view.getRenderAttributeString( badgeKey ) }}}>{{{ item.item_badge }}}</span>
                                                                                            <# } #>
                                                                            </span>

                                                                            <# if ( hasSubmenu && dropdownIcon && dropdownIcon.value ) { #>
                                                                                <span {{{ view.getRenderAttributeString( dropdownKey ) }}}>
                                                                                    {{{ dropdownIcon.value }}}
                                                                                    <# if ( dropdownIconActive && dropdownIconActive.value ) { #>
                                                                                        {{{ dropdownIconActive.value }}}
                                                                                        <# } #>
                                                                                </span>
                                                                                <# } #>

                                                                                    <# if ( hasSubmenu && fullscreenDropdownIcon && fullscreenDropdownIcon.value ) { #>
                                                                                        <span {{{ view.getRenderAttributeString( dropdownKey + '-fullscreen' ) }}}>
                                                                                            {{{ fullscreenDropdownIcon.value }}}
                                                                                            <# if ( fullscreenDropdownIconActive && fullscreenDropdownIconActive.value ) { #>
                                                                                                {{{ fullscreenDropdownIconActive.value }}}
                                                                                                <# } #>
                                                                                        </span>
                                                                                        <# } #>
                                                        </a>
                                                        <div {{{ view.getRenderAttributeString( submenuKey ) }}}></div>
                                                    </li>
                                                    <# } #>
                                                        <# } ); } #>
                                    </ul>
                                    <# if ( menuItems.length ) {
                                        _.each( menuItems, function( item, index ) {
                                        const itemCount=index + 1,
                                        itemUid=elementUid + itemCount,
                                        itemConvertToWidget=item.item_convert_to_widget && item.item_convert_to_widget==='yes' ,
                                        widgetKey='widget-' + itemUid,
                                        widgetContainerId='ui-e-widget-area-' + itemUid;

                                        if ( ! itemConvertToWidget ) {
                                        return;
                                        }

                                        view.addRenderAttribute( widgetKey, { 'class' : [ 'ui-e-widget-area-placeholder' ], 'data-container-id' : widgetContainerId, 'data-menu-index' : itemCount,
                                        } );
                                        #>
                                        <div {{{ view.getRenderAttributeString( widgetKey ) }}}></div>
                                        <# } ); } #>
                                </div>
                </nav>
            <?php
        }

        protected function content_template_single_repeater_item()
        {
            ?>
                <#
                    itemCount=view.collection.length + 1,
                    elementUid=view.getIDInt().toString(),
                    itemUid=elementUid + itemCount,
                    item=data,
                    itemKey='item-' + itemUid,
                    linkKey='link-' + itemUid,
                    imageKey='image-' + itemUid,
                    textKey='text-' + itemUid,
                    titleKey='title-' + itemUid,
                    descriptionKey='description-' + itemUid,
                    badgeKey='badge-' + itemUid,
                    iconKey='icon-' + itemUid,
                    dropdownKey='dropdown-' + itemUid,
                    submenuKey='submenu-' + itemUid,
                    itemIcon=item.item_icon && item.item_icon.value
                    ? elementor.helpers.renderIcon( view, item.item_icon, { 'aria-hidden' : true, 'class' : 'ui-e-menu-item-icon' }, 'i' , 'object' )
                    : false,
                    dropdownIcon=settings.dropdown_icon && settings.dropdown_icon.value
                    ? elementor.helpers.renderIcon( view, settings.dropdown_icon, { 'aria-hidden' : true, 'class' : 'ui-e-dropdown-closed' }, 'i' , 'object' )
                    : false,
                    dropdownIconActive=settings.dropdown_icon_active && settings.dropdown_icon_active.value
                    ? elementor.helpers.renderIcon( view, settings.dropdown_icon_active, { 'aria-hidden' : true, 'class' : 'ui-e-dropdown-opened' }, 'i' , 'object' )
                    : false,
                    fullscreenDropdownIcon=settings.dropdown_fullscreen_icon && settings.dropdown_fullscreen_icon.value
                    ? elementor.helpers.renderIcon( view, settings.dropdown_fullscreen_icon, { 'aria-hidden' : true, 'class' : 'ui-e-dropdown-closed' }, 'i' , 'object' )
                    : false,
                    fullscreenDropdownIconActive=settings.dropdown_fullscreen_icon_active && settings.dropdown_fullscreen_icon_active.value
                    ? elementor.helpers.renderIcon( view, settings.dropdown_fullscreen_icon_active, { 'aria-hidden' : true, 'class' : 'ui-e-dropdown-opened' }, 'i' , 'object' )
                    : false,
                    imageData=item.item_image && item.item_image.url
                    ? {
                    id: item.item_image.id,
                    url: item.item_image.url,
                    size: 'thumbnail' ,
                    model: view.getEditModel()
                    }
                    : false,
                    imageUrl=imageData ? elementor.imagesManager.getImageUrl( imageData ) : '' ,
                    itemConvertToWidget=item.item_convert_to_widget && item.item_convert_to_widget==='yes' ,
                    hasSubmenu=item.item_submenu && item.item_submenu==='yes' ,
                    submenuWidthClass='ui-e-' + ( item.item_submenu_width || 'widget-width' ) ,
                    submenuId='ui-e-menu-content-' + itemUid,
                    widgetKey='widget-' + itemUid,
                    widgetContainerId='ui-e-widget-area-' + itemUid;

                    const itemClasses=[ 'ui-e-menu-item' ];

                    if ( itemConvertToWidget ) {
                    itemClasses.push( 'ui-e-converted-widget-placeholder' );
                    }

                    if ( hasSubmenu ) {
                    itemClasses.push( 'ui-e-has-submenu' );
                    itemClasses.push( submenuWidthClass );
                    }

                    view.addRenderAttribute( itemKey, { 'class' : itemClasses,
                    }, null, true );
                    view.addRenderAttribute( itemKey, 'data-menu-content-id' , submenuId, true );
                    view.addRenderAttribute( itemKey, 'data-menu-index' , itemCount, true );

                    if ( item.element_id ) {
                    view.addRenderAttribute( itemKey, 'id' , item.element_id, true );
                    }

                    const linkAttrs={};

                    if ( item.item_link && item.item_link.url ) {
                    linkAttrs.href=item.item_link.url;
                    }

                    if ( item.item_link && item.item_link.is_external ) {
                    linkAttrs.target='_blank' ;
                    linkAttrs.rel='noopener noreferrer' ;
                    }

                    if ( item.item_link && item.item_link.nofollow ) {
                    linkAttrs.rel=( linkAttrs.rel ? linkAttrs.rel + ' ' : '' ) + 'nofollow' ;
                    }

                    view.addRenderAttribute( linkKey, linkAttrs, null, true );

                    view.addRenderAttribute( titleKey, { 'data-binding-type' : 'repeater-item' , 'data-binding-repeater-name' : 'menu_items' , 'data-binding-setting' : [ 'item_title' , 'element_id' ], 'data-binding-index' : itemCount, 'data-binding-config' : JSON.stringify({ 'element_id' : { editType : 'attribute' , attr : 'id' , selector : 'li' }, 'item_title' : { editType : 'text' } }),
                    }, null, true );

                    view.addRenderAttribute( badgeKey, { 'class' : [ 'ui-e-badge' ], 'data-binding-type' : 'repeater-item' , 'data-binding-repeater-name' : 'menu_items' , 'data-binding-setting' : [ 'item_badge' ], 'data-binding-index' : itemCount, 'data-binding-config' : JSON.stringify({ 'item_badge' : { editType : 'text' } }),
                    }, null, true );

                    view.addRenderAttribute( imageKey, { 'class' : [ 'ui-e-menu-item-image' ] }, null, true );

                    view.addRenderAttribute( textKey, { 'class' : [ 'ui-e-text' ] }, null, true );

                    view.addRenderAttribute( iconKey, { 'class' : [ 'ui-e-svg-wrap' , 'ui-e-menu-item-icon' ], 'data-binding-type' : 'repeater-item' , 'data-binding-repeater-name' : 'menu_items' , 'data-binding-setting' : [ 'item_icon.value' ], 'data-binding-index' : itemCount, 'data-binding-config' : JSON.stringify({ 'item_icon.value' : { editType : 'text' } }),
                    }, null, true );

                    view.addRenderAttribute( descriptionKey, { 'class' : [ 'ui-e-description' ], 'data-binding-type' : 'repeater-item' , 'data-binding-repeater-name' : 'menu_items' , 'data-binding-setting' : [ 'item_description' ], 'data-binding-index' : itemCount, 'data-binding-config' : JSON.stringify({ 'item_description' : { editType : 'text' } }),
                    }, null, true );

                    view.addRenderAttribute( dropdownKey, { 'class' : [ 'ui-e-svg-wrap' , 'ui-e-dropdown-icon' ] }, null, true );
                    view.addRenderAttribute( dropdownKey + '-fullscreen' , { 'class' : [ 'ui-e-svg-wrap' , 'ui-e-dropdown-icon' ], 'data-fullscreen-layout' : true }, null, true );

                    view.addRenderAttribute( submenuKey, { 'class' : [ 'ui-e-submenu-placeholder' ], 'data-container-id' : submenuId, 'data-menu-index' : itemCount, 'hidden' : hasSubmenu ? false : 'hidden'
                    }, null, true );

                    if ( itemConvertToWidget ) {
                    view.addRenderAttribute( widgetKey, { 'class' : [ 'ui-e-widget-area-placeholder' ], 'data-container-id' : widgetContainerId, 'data-menu-index' : itemCount,
                    }, null, true );
                    }

                    #>

                    <# if ( itemConvertToWidget ) { #>
                        <li {{{ view.getRenderAttributeString( itemKey ) }}} style="display:none;"></li>
                        <div {{{ view.getRenderAttributeString( widgetKey ) }}}></div>
                        <# } else { #>
                            <li {{{ view.getRenderAttributeString( itemKey ) }}}>
                                <a {{{ view.getRenderAttributeString( linkKey ) }}}>
                                    <# if ( imageUrl ) { #>
                                        <img {{{ view.getRenderAttributeString( imageKey ) }}} src="{{{ imageUrl }}}" alt="{{{ item.item_image.alt || '' }}}" />
                                        <# } #>

                                            <# if ( itemIcon && itemIcon.value ) { #>
                                                <span {{{ view.getRenderAttributeString( iconKey ) }}}>
                                                    {{{ itemIcon.value }}}
                                                </span>
                                                <# } #>

                                                    <span {{{ view.getRenderAttributeString( textKey ) }}}>
                                                        <span {{{ view.getRenderAttributeString( titleKey ) }}}>{{{ item.item_title }}}</span>

                                                        <# if ( item.item_description ) { #>
                                                            <span {{{ view.getRenderAttributeString( descriptionKey ) }}}>{{{ item.item_description }}}</span>
                                                            <# } #>

                                                                <# if ( item.item_badge ) { #>
                                                                    <span {{{ view.getRenderAttributeString( badgeKey ) }}}>{{{ item.item_badge }}}</span>
                                                                    <# } #>
                                                    </span>

                                                    <# if ( hasSubmenu && dropdownIcon && dropdownIcon.value ) { #>
                                                        <span {{{ view.getRenderAttributeString( dropdownKey ) }}}>
                                                            {{{ dropdownIcon.value }}}
                                                            <# if ( dropdownIconActive && dropdownIconActive.value ) { #>
                                                                {{{ dropdownIconActive.value }}}
                                                                <# } #>
                                                        </span>
                                                        <# } #>

                                                            <# if ( hasSubmenu && fullscreenDropdownIcon && fullscreenDropdownIcon.value ) { #>
                                                                <span {{{ view.getRenderAttributeString( dropdownKey + '-fullscreen' ) }}}>
                                                                    {{{ fullscreenDropdownIcon.value }}}
                                                                    <# if ( fullscreenDropdownIconActive && fullscreenDropdownIconActive.value ) { #>
                                                                        {{{ fullscreenDropdownIconActive.value }}}
                                                                        <# } #>
                                                                </span>
                                                                <# } #>
                                </a>
                                <div {{{ view.getRenderAttributeString( submenuKey ) }}}></div>
                            </li>
                            <# } #>
                        <?php
                    }

                    protected function get_initial_config(): array
                    {
                        if (Plugin::$instance->experiments->is_feature_active('e_nested_atomic_repeaters')) {
                            return array_merge(parent::get_initial_config(), [
                                'support_improved_repeaters' => true,
                                'target_container' => ['.ui-e-menu-list'],
                                'is_interlaced' => true,
                            ]);
                        }

                        return array_merge(parent::get_initial_config(), [
                            'is_interlaced' => true,
                        ]);
                    }
                }
                \Elementor\Plugin::instance()->widgets_manager->register(new Advanced_Menu());

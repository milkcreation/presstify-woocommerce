<?php
/**
 * @var tiFy\Contracts\View\ViewController $this
 *
 * @var array $args Arguments du champ.
 * @var string $key Nom du champ.
 * @var mixed $value Valeur du champ.
 */
echo field(
    'select-js',
    [
        'before'    => $this->get('args.before'),
        'after'     => $this->get('args.after'),
        'attrs'     => $this->get('args.attrs', []),
        'name'      => esc_attr($key),
        'value'     => $value,
        'disabled'  => $this->get('args.disabled', false),
        'choices'   => $this->get('args.choices', []),
        'source'    => $this->get('args.sources', false),
        'multiple'  => $this->get('args.multiple', false),
        'removable' => $this->get('args.removable', true),
        'max'       => $this->get('args.max', - 1),
        'sortable'  => $this->get('args.sortable', false),
        'trigger'   => $this->get('args.trigger', true),
        'picker'    => $this->get('args.picker', []),
        'viewer'    => $this->get('args.viewer', [])
    ]
);
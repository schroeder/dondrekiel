<?php

namespace DondrekielAdminBundle\EventListener;

// ...

use DondrekielAdminBundle\Model\MenuItemModel;
use Avanzu\AdminThemeBundle\Event\SidebarMenuEvent;
use Symfony\Component\HttpFoundation\Request;

class AdminMenuItemListListener
{

    public function onSetupMenu(SidebarMenuEvent $event)
    {
        $request = $event->getRequest();
        foreach ($this->getMenu($request) as $item) {
            $event->addItem($item);
        }
    }

    protected function getMenu(Request $request)
    {
        $menuItems = array(
            $menuItem00 = new MenuItemModel('Übersicht', 'Übersicht', 'admingameteamstatus', array(/* options */), 'iconclasses fa fa-bullseye'),
            //$menuItem01 = new MenuItemModel('Log-Buch', 'Log-Buch', 'actionlog_index', array(/* options */), 'iconclasses fa fa-film'),
            $menuItem03 = new MenuItemModel('Teams', 'Teams', 'team_index', array(/* options */), 'iconclasses fa fa-list'),
            $menuItem04 = new MenuItemModel('Stationen', 'Stationen', 'station_index', array(/* options */), 'iconclasses fa fa-list'),
            $menuItem05 = new MenuItemModel('TeamNachrichtSchicken', 'Nachricht schicken', 'team_message', array(/* options */), 'iconclasses fa fa-commenting')
        );


        return $this->activateByRoute($request->get('_route'), $menuItems);
    }

    protected function activateByRoute($route, $items)
    {
        foreach ($items as $item) {
            if ($item->hasChildren()) {
                $this->activateByRoute($route, $item->getChildren());
            } else {
                if ($item->getRoute() == $route) {
                    $item->setIsActive(true);
                }
            }
        }
        return $items;
    }
}

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
            $menuItem01 = new MenuItemModel('Log-Buch', 'Log-Buch', 'actionlog_index', array(/* options */), 'iconclasses fa fa-film'),
            $menuItem02 = new MenuItemModel('Punktestand', 'Punktestand', 'todo', array(/* options */), 'iconclasses fa fa-send'),
            $menuItem03 = new MenuItemModel('Gruppen', 'Gruppen', 'todo', array(/* options */), 'iconclasses fa fa-group'),
            $menuItem04 = new MenuItemModel('Spiele', 'Spiele', 'todo', array(/* options */), 'iconclasses fa  fa-futbol-o'),
            $menuItem05 = new MenuItemModel('AnzeigeStandLevel', 'Anzeige Stand Level', 'todo', array(/* options */), 'iconclasses fa fa-flag'),
            $menuItem06 = new MenuItemModel('PasscodeGenerieren', 'Passcode generieren', 'todo', array(/* options */), 'iconclasses fa fa-qrcode')
        );
        $menuItem03->addChild(new MenuItemModel('MitgliederListe', 'Liste', 'group_index', array(/* options */), 'iconclasses fa fa-list'));
        $menuItem03->addChild(new MenuItemModel('MitgliederVerwalten', 'Mitglieder verwalten', 'todo', array(/* options */), 'iconclasses fa fa-edit'));
        $menuItem03->addChild(new MenuItemModel('MitgliederNachrichtSchicken', 'Nachricht schicken', 'group_message', array(/* options */), 'iconclasses fa fa-commenting'));
        $menuItem04->addChild(new MenuItemModel('SpieleListe', 'Liste', 'game_index', array(/* options */), 'iconclasses fa fa-list'));
        $menuItem04->addChild(new MenuItemModel('SpieleNachrichtSchicken', 'Nachricht schicken', 'game_message', array(/* options */), 'iconclasses fa fa-commenting'));
        $menuItem05->addChild(new MenuItemModel('PasscodeGenerierenSpiel', 'Spiel', 'todo', array(/* options */), 'iconclasses fa fa-futbol-o'));
        $menuItem05->addChild(new MenuItemModel('PasscodeGenerierenGruppe', 'Gruppe', 'admin_team_passcode', array(/* options */), 'iconclasses fa fa-users'));
        $menuItem06->addChild(new MenuItemModel('PasscodeGenerierenTeilnehmer', 'Teilnehmer', 'admin_member_passcode', array(/* options */), 'iconclasses fa fa-user'));
        $menuItem06->addChild(new MenuItemModel('PasscodeGenerierenAdmin', 'Administrator', 'todo', array(/* options */), 'iconclasses fa fa-linux'));

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

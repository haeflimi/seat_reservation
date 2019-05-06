
<div id="seat-map-<?=$bID?>" class="seat-map-wrapper">
    <?php echo $svgMap ?>

    <div id="seat-map-reservations" hidden data-class="<?=$class?>">
        <?php foreach($reservations as $key => $u):?>
            <div id="<?=$key.'_details'?>" class="seat-map-reservation-item"
                 data-uid="<?=$u->getUserId()?>"
                 data-seat="<?=$key?>"
                 data-title="<?=t('Seat')?>: <b><?=strtoupper($key)?></b>"
                 data-svgelement="<?=$key?>"
                 data-myseat="<?=($key == $mySeat)?true:false;?>">
                <?php if($key == $mySeat): ?>
                    <p><?=t('This Seat is your Seat')?></p>
                <?php else: ?>
                    <p><?=t('This Seat is taken by:').' <a class="btn btn-outline-primary btn-round" type="button" data-toggle="modal" data-target="#modal" data-source="/members/profile/'.$u->getUserID().'"><i class="fa fa-user"></i> '.$u->getUserName().'</a>'?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if($showSearch): ?>
    <h2>Filter:</h2>
    <div class="form-group">
        <select id="seat-map-filter" class="form-control">
                <option value="">Auswählen</option>
            <?php foreach($reservations as $key => $u): ?>
                <option value="<?=$key?>"><?=$u->getUserName()?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif;?>

    <?php if($showList): ?>
    <div class="seat-map-list">
        <h2>Teilnehmerliste:</h2>
        <?php foreach($reservations as $key => $u):?>
            <?=View::element('participant-list', array('user' => $u), 'turicane_theme');?></p>
        <?php endforeach; ?>
    </div>
    <?php endif;?>

    <div id="seat-map-empty-seat-form" hidden>
        <p><?=t('This Seat is available.')?></p>
        <?php if($allowReservation):?>
        <form action="<?=$this->action('claim_seat')?>" method="post">
            <input id="seat-map-claim-seat" name="claim_seat_id" type="hidden" value=""/>
            <button class="btn btn-default" data-seat=""><?=t('Claim now!')?></button>
        </form>
        <?php endif; ?>
    </div>
    <style>
        .seat-map-wrapper svg .<?=$class?> *{
            fill: green;
            cursor: pointer;
        }
        .seat-map-wrapper svg .<?=$class?>.taken *{
            fill: red;
            cursor: pointer;
        }
        .seat-map-wrapper svg .<?=$class?>.my *{
            fill: orange;
            cursor: pointer;
        }
    </style>
</div>



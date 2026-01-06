<section id="links" class="main style3 secondary home">
    <div class="content">
        <header>
            <h2><?php echo __('Контакты и ссылки'); ?></h2>
            <p><?php echo __('Больше информации и контента от Andrew Old можно найти в социальных сетях по ссылкам ниже. Также имеется email для связи с администратором портала и форма для мыслей и предложений'); ?></p>

            <ul class="icons" style="font-size: 150%">
                <li><a href="https://www.tiktok.com/@andrewww_old" class="icon brands fa-tiktok" target="_blank"><span class="label">Tiktok</span></a></li>
                <li><a href="https://www.facebook.com/100001684488336/timeline" class="icon brands fa-facebook-f" target="_blank"><span class="label">Facebook</span></a></li>
                <li><a href="https://www.instagram.com/andrewww_old/" class="icon brands fa-instagram" target="_blank"><span class="label">Instagram</span></a></li>
                <li>
                    <a href="mailto:dialectik1@gmail.com" class="icon solid fa-envelope">
                        <span class="label">Email</span>
                    </a>
                </li>
            </ul>
        </header>
        <div class="box">
            <form method="post" action="{{ route('contact.send') }}">
                @csrf
                <div class="fields">
                    <div class="field half"><input type="text" name="name" placeholder="<?php echo __('Имя'); ?>" /></div>
                    <div class="field half"><input type="email" name="email" placeholder="Email" /></div>
                    <div class="field"><textarea name="message" placeholder="<?php echo __('Сообщение'); ?>" rows="6"></textarea></div>
                </div>
                <ul class="actions special">
                    <li><input type="submit" value="<?php echo __('Отправить сообщение'); ?>" /></li>
                </ul>
            </form>
        </div>
    </div>
    <footer class="down">
        <a href="#intro" class="button up"><?php echo __('Начало'); ?></a>
    </footer>
</section>
<section class="section section-md bg-default" id="menu">
    <div class="container">
        <h3 class="oh-desktop"><span class="d-inline-block wow slideInDown">Menú</span></h3>
        <div class="row row-md row-30">

            @foreach ($categories as $category)
                <div class="col-6 col-lg-4">
                    <div class="oh-desktop">
                        <!-- Services Terri-->
                        <article class="services-terri wow slideInUp">
                            <div class="services-terri-figure">
                              <img src="{{ asset($category->image) }}" alt="{{$category->name}}"
                                    width="370" height="278" loading="lazy" />
                            </div>
                            <div class="services-terri-caption">
                              <span class="services-terri-icon linearicons-{{$category->icon}}"></span>
                                <h5 class="services-terri-title">
                                  <a href="{{route('categories.display',$category->id)}}">{{$category->name}}</a>
                                </h5>
                            </div>
                        </article>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</section>
